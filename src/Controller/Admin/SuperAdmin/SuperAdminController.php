<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Utils\Interfaces\UploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/su")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @Route("/upload-video-locally", name="upload_video_locally")
     */
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {

            $entityManager = $this->getDoctrine()->getManager();

            $file = $video->getUploadedVideo();
            $fileName = $fileUploader->upload($file);

            $basePath = Video::uploadFolder;
            $video->setPath($basePath.$fileName[0]);
            $video->setTitle($fileName[1]);

            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('videos');
        }



        return $this->render('admin/upload_video_locally.html.twig', [
            'form' => $form->createView()
        ]);
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