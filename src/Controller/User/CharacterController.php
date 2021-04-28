<?php

namespace App\Controller\User;

use App\Entity\Character;
use App\Form\CharacterType;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

			return $this->redirectToRoute('user_account');
		}

        return $this->render('user/character/action.html.twig', [
			'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Character $character): Response
    {
		if(!$this->getUser()->hasCharacter($character)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$form = $this->createForm(CharacterType::class, $character);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$character->setUpdatedAt(new DateTime());
            $this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('user_account');
		}

        return $this->render('user/character/action.html.twig', [
            'controller_name' => 'CharacterController edit',
			'form' => $form->createView(),
        ]);
    }

	/**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(Character $character): Response
    {
		if(!$this->getUser()->hasCharacter($character)) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$this->getDoctrine()->getManager()->remove($character);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute('user_account');
    }
}
