<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request): Response
    {
        $message = new Message();
        $message->setCreatedAt(new DateTime());

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message = $form->getData();
            $this->getDoctrine()->getManager()->persist($message);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your message has been sent');

            return $this->redirectToRoute('home');
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
