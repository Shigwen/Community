<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'home';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $credentials['email']
        ]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException("Your login credentials don't match an account in our system");
        }

        if ($user->getStatus() === User::STATUS_BAN) {
            throw new CustomUserMessageAuthenticationException('Your account haved be banned');
        }

        if ($user->getStatus() === User::STATUS_WAITING_EMAIL_CONFIRMATION) {
            throw new CustomUserMessageAuthenticationException('You must confirm your email address before you can log in');
        }

        return $user;
    }

    /**
     * Check the user password and the number of user login attempts
     * After x attempts the user can no longer try to connect
     * He will have to wait x minutes before trying again
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $passValid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        $intervalBtwAttempt = date_create('now')->diff($user->getLastAttempt());

        if (intval($intervalBtwAttempt->i) > User::DELAY_AFTER_MAX_ATTEMPT) {
            $user->setNbrOfAttempt(0);
        }

        if ($user->getNbrOfAttempt() >= User::NUMBER_MAX_OF_ATTEMPT) {
            throw new CustomUserMessageAuthenticationException(
                'You have tried logging in too many times. Please wait ' . User::DELAY_AFTER_MAX_ATTEMPT . ' minutes before retrying.'
            );
        }

        $user->setLastAttempt(new DateTime());

        if (!$passValid) {
            $user->setNbrOfAttempt($user->getNbrOfAttempt() + 1);
        } else {
            $user->setNbrOfAttempt(0);
        }

        $this->entityManager->flush();
        return $passValid;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('user_account'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
