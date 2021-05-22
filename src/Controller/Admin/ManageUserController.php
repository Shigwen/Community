<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function promoteOrDemote(User $user): Response
    {
		if ($user->getStrRole() === User::ROLE_USER) {
			$user->setRoles([User::ROLE_RAID_LEADER]);
		} else if ($user->getStrRole() === User::ROLE_RAID_LEADER) {
			$user->setRoles([User::ROLE_USER]);
		}

		$this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_users');
    }

}
