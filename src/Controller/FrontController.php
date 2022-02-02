<?php

namespace App\Controller;

use App\Controller\Traits\Likes;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\Interfaces\CacheInterface;
use App\Utils\VideoForNoValidSubscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    use Likes;
    /**
     * @Route("/", name="main_page")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/video-list/category/{categoryname},{id}/{page}", defaults={"page":"1"}, name="video_list")
     */
    public function videoList($id, $page, CategoryTreeFrontPage $categories, Request $request, VideoForNoValidSubscription $videoNoMembers, CacheInterface $cache): Response
    {
        $cache = $cache->cache;
        $videoList = $cache->getItem('video_list'.$id.$page.$request->get('sortby'));
        // $video_list->tag(['video_list']);
        $videoList->expiresAfter(60);


        if(!$videoList->isHit())
        {
            $ids = $categories->getChildIds($id);
            array_push($ids, $id);

            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByChildIds($ids ,$page, $request->get('sortby'));

            $categories->getCategoryListAndParent($id);
            $response = $this->render('front/video_list.html.twig',[
                'subcategories' => $categories,
                'videos'=>$videos,
                'videoNoMembers' => $videoNoMembers->check()
            ]);

            $videoList->set($response);
            $cache->save($videoList);
        }

        return $videoList->get();
    }

    /**
     * @Route("/video-details/{video}", name="video_details")
     */
    public function videoDetails(VideoRepository $repository, $video, VideoForNoValidSubscription $videoNoMembers): Response
    {

        return $this->render('front/video_details.html.twig', [
            'video' => $repository->videoDetails($video),
            'videoNoMembers' => $videoNoMembers->check()
        ]);
    }

    /**
     * @Route("/search-results/{page}",defaults={"page":"1"}, methods={"GET"}, name="search_results")
     */
    public function searchResults($page, Request $request, VideoForNoValidSubscription $videoNoMembers): Response
    {
        $videos = null;
        $query = null;

        if ($query = $request->get('query')) {
            $videos = $this->getDoctrine()->getRepository(Video::class)
                ->findByTitle($query, $page, $request->get('sortby'));

            if(!$videos->getItems()) $videos = null;
        }
        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,
            'videoNoMembers' => $videoNoMembers->check()
        ]);
    }

    /**
     * @Route("/new-comment/{video}", methods={"POST"}, name="new_comment")
     */
    public function newComment(Video $video, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!empty(trim($request->request->get('comment')))) {
            $comment = new Comment();
            $comment->setContent($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('video_details', ['video' => $video->getId()]);
    }

    /**
     * @Route("/delete-comment/{comment}", name="delete_comment")
     * @Security("user.getId() == comment.getUser().getId()")
     */
    public function deleteComment(Comment $comment, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/video-list/{video}/like", name="like_video", methods={"POST"})
     * @Route("/video-list/{video}/dislike", name="dislike_video", methods={"POST"})
     * @Route("/video-list/{video}/unlike", name="undo_like_video", methods={"POST"})
     * @Route("/video-list/{video}/undodislike", name="undo_dislike_video", methods={"POST"})
     */
    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch($request->get('_route'))
        {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;

            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json(['action' => $result,'id'=>$video->getId()]);
    }

    public function mainCategories(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findBy(['parent' => NULL], ['name' => 'ASC']);
        return $this->render('front/_main_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}