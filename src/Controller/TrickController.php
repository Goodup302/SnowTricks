<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\TagType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    const UNKNOWN_ERROR = 'Une erreur inconnue est survenue';
    const NOT_CONNECTED = "Vous devez ètre connecté pour poster des commentaires";
    const MAX_COMMENT_PER_PAGE = 10;
    const TRICK_PER_LOAD = 12;

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TrickController constructor.
     * @param TrickRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(TrickRepository $repository, EntityManagerInterface $em)
    {
        $this->trickRepository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/trick_list", name="trick.list", methods="POST")
     */
    public function list(Request $request): Response
    {
        $offset = intval($request->request->get('offset'));
        $tricks = $this->trickRepository->loadMore($offset, self::TRICK_PER_LOAD);

        $end = false;
        if (($offset + sizeof($tricks)) == $this->trickRepository->countPublish()) $end = true;

        return $this->render('ajax/trick.html.twig', ['tricks' => $tricks, 'end' => $end]);
    }

    /**
     * @Route("/trick/{slug}", name="trick.single", methods="GET|POST")
     * @param Trick $trick
     * @return Response
     */
    public function single(Trick $trick, Request $request, CommentRepository $commentRepository): Response
    {
        if ($trick->isCreated() == false) return $this->redirectToRoute("home");
        //FORM
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user != null) {
                $comment->setTrick($trick);
                $comment->setUser($user);
                $comment->setPublishDate(new \DateTime());
                $this->em->persist($comment);
                $this->em->flush();
                $form = $this->createForm(CommentType::class, new Comment());
                $this->addFlash("success", "Votre commentaire a été posté !");
            }
        }
        return $this->render('trick/single.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
            'commentPage' => ceil($commentRepository->countByTrick($trick->getId()) / self::MAX_COMMENT_PER_PAGE),
        ]);
    }


    /**
     * @Route("/comment/{id}", name="trick.comments", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function commentByPage(Trick $trick, Request $request, CommentRepository $commentRepository): Response
    {
        $page = intval($request->request->get('page'));
        $comments = $commentRepository->getPaginate($trick->getId(), $page, self::MAX_COMMENT_PER_PAGE);
        return $this->render('ajax/comment.html.twig', ['comments' => $comments]);
    }


    /*ADMIN PART*/

    /**
     * @Route("/edit/{slug}", name="trick.edit", methods="GET|POST")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request): Response
    {
        //Upload Video form
        $video = new Video();
        $videoOption = ['attr' => [
            'action' => $this->generateUrl('video.add', ['id' => $trick->getId()])
        ]];
        $videoForm = $this->createForm(VideoType::class, $video, $videoOption);
        //Upload Image form
        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);
        //form
        $form = $this->createForm(TrickType::class, $trick, ['attr' => [TrickType::TRICK => $trick->getId()]]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($trick->isCreated()) $trick->setLastEdit(new \DateTime());
            if (!$trick->isCreated()) $trick->setPublishDate(new \DateTime());
            $trick->setSlug(Utils::slugify($trick->getName()));
            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash("success", "La figure a bien été éditée !");
            return $this->redirectToRoute('trick.single', ['slug' => $trick->getSlug()]);
        }
        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'imageForm' =>$imageForm->createView(),
            'videoForm' =>$videoForm->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/new", name="trick.new", methods="GET|POST")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $name = $request->request->get('name');
        if ($name != null) {
            if (!$this->trickRepository->findOneBy(['name' => $name])) {
                $trick = new Trick();
                $trick->setName($name);
                $trick->setSlug(Utils::slugify($trick->getName()));
                $this->em->persist($trick);
                $this->em->flush();
                return $this->redirectToRoute("trick.edit", ['slug' => $trick->getSlug()]);
            } else {
                $this->addFlash('error', 'Le nom saisi est déjà utilisé !');
                return $this->redirectToRoute("home");
            }
        }
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/delete/{id}", name="trick.delete", methods="DELETE")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Trick $trick
     * @return Response
     */
    public function delete(Trick $trick): Response
    {
        try {
            foreach ($trick->getComments() as $comment) {
                $this->em->remove($comment);
            }
            foreach ($trick->getImages() as $image) {
                $this->em->remove($image);
            }
            foreach ($trick->getVideos() as $video) {
                $this->em->remove($video);
            }
            $this->em->remove($trick);
            $this->em->flush();
            $this->addFlash("success", "La figure a bien été supprimée !");
            return new JsonResponse(array(
                'success' => true,
                'url' => $this->generateUrl('home'),
            ));
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'url' => $this->generateUrl('home'),
                'message' => self::UNKNOWN_ERROR,
                'error' => $e->getMessage(),
            ));
        }
    }

    /**
     * @Route("/addtag", name="tag.add", methods="POST|GET")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return Response
     */
    public function addTag(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $this->em->persist($trick);
            $this->em->flush();
        }
        try {
            $this->em->persist($trick);
            $this->em->flush();
            return new JsonResponse(array(
                'success' => true,
                'url' => $this->generateUrl('home'),
            ));
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'success' => false,
                'url' => $this->generateUrl('home'),
                'message' => self::UNKNOWN_ERROR,
                'error' => $e->getMessage(),
            ));
        }
    }
}
