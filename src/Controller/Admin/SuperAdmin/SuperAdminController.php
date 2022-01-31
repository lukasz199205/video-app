<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/su")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @Route("/upload-video", name="upload_video")
     */
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users(): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findBy([], ['name'=>'ASC']);

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/delete-user/{user}", name="delete_user")
     */
    public function deleteUser(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->redirectToRoute('users');
    }
}