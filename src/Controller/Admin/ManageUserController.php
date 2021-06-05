<?php

namespace App\Controller\Admin;

use App\Entity\Raid;
use App\Entity\User;
use App\Service\Raid\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class ManageUserController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function users(): Response
    {
        $banned = ($this->getUser()->getStrRole() === User::ROLE_OWNER) ?
            $this->getDoctrine()->getRepository(User::class)->findBy(['status' => User::STATUS_BAN]) : [];

        $admins = ($this->getUser()->getStrRole() === User::ROLE_OWNER) ?
            $this->getDoctrine()->getRepository(User::class)->findByRole(User::ROLE_ADMIN) : [];

        return $this->render('admin_owner/user_list.html.twig', [
            'users' => $this->getDoctrine()->getRepository(User::class)->findByRole(User::ROLE_USER),
            'raidLeaders' => $this->getDoctrine()->getRepository(User::class)->findByRole(User::ROLE_RAID_LEADER),
            'admins' => $admins,
            'banned' => $banned,
        ]);
    }

    /**
     * @Route("/promote-or-demote/user/{id}", name="promote_or_demote")
     */
    public function promoteOrDemote(User $user, Template $template): Response
    {
        if ($user->getStrRole() === User::ROLE_USER) {
            $user->setRoles([User::ROLE_RAID_LEADER]);
            if (!$this->getDoctrine()->getRepository(Raid::class)->getRaidTemplateByUser($user)) {
                $template->createDefaultTemplate($user);
            }
        } else if ($user->getStrRole() === User::ROLE_RAID_LEADER) {
            $user->setRoles([User::ROLE_USER]);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_users');
    }
}
