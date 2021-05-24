<?php

namespace App\Controller\User;

use DateTime;
use App\Entity\Character;
use App\Form\CharacterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/user/character", name="user_character_")
 */
class CharacterController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request): Response
    {
		$character = new Character();
		$character->setUser($this->getUser());

		$form = $this->createForm(CharacterType::class, $character);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$character = $form->getData();
            $this->getDoctrine()->getManager()->persist($character);
            $this->getDoctrine()->getManager()->flush();

		}

		return $this->redirectToRoute('user_account');
    }

	/**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Character $character): Response
    {
		if(!$this->getUser()->hasCharacter($character)) {
			throw new AccessDeniedHttpException();
		}

		$form = $this->createForm(CharacterType::class, $character);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$character->setUpdatedAt(new DateTime());
            $this->getDoctrine()->getManager()->flush();
		}

		return $this->redirectToRoute('user_account');
    }

	/**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Character $character): Response
    {
		if(!$this->getUser()->hasCharacter($character)) {
			throw new AccessDeniedHttpException();
		}

		$this->getDoctrine()->getManager()->remove($character);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('user_account');
    }
}
