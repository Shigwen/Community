<?php

namespace App\Controller\Owner;

use App\Entity\Raid;
use App\Entity\User;
use App\Service\Raid\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/owner", name="owner_")
 */
class ManageUserController extends AbstractController
{
    /**
     * @Route("/ban-hammer/{id}", name="ban_hammer")
     */
    public function banHammer(User $user): Response
    {
        if ($user->getStatus() === User::STATUS_EMAIL_CONFIRMED) {
            $user->setStatus(User::STATUS_BAN);
        } else if ($user->getStatus() === User::STATUS_BAN) {
            $user->setStatus(User::STATUS_EMAIL_CONFIRMED);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/change-role/{id}", name="change_role")
     */
    public function changeRole(Request $request, User $user, Template $template): Response
    {
        $role = $request->request->get('role');

        if (!in_array($role, [User::ROLE_USER, User::ROLE_RAID_LEADER, User::ROLE_ADMIN])) {
            throw $this->createNotFoundException('Une erreur est survenue');
        }

        if (
            $role === User::ROLE_RAID_LEADER &&
            !$this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($user)
        ) {
            $template->createDefaultTemplate($user);
        }

        $user->setRoles([$role]);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_users');
    }
}
