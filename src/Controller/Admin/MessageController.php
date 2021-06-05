<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function messages(): Response
    {
        return $this->render('admin_owner/message/message_list.html.twig', [
            'messages' => $this->getDoctrine()->getRepository(Message::class)->findByUnarchivedMessages(),
        ]);
    }

    /**
     * @Route("/archived-messages", name="archived_messages")
     */
    public function archivedMessages(): Response
    {
        return $this->render('admin_owner/message/archived_message_list.html.twig', [
            'messages' => $this->getDoctrine()->getRepository(Message::class)->findByArchivedMessages(),
        ]);
    }

    /**
     * @Route("/messages/{id}", name="message")
     */
    public function message(Message $message): Response
    {
        return $this->render('admin_owner/message/message.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/archive-message/{id}", name="archive_message")
     */
    public function archiveMessage(Message $message): Response
    {
        if ($message->getArchivedAt()) {
            $message->setArchivedAt(null);
        } else {
            $message
                ->setArchivedAt(new DateTime())
                ->setArchivedBy($this->getUser());
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_messages');
    }
}
