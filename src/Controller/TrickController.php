<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\TagType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\TrickRepository;
use App\Service\Date;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\ka_GE\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TrickController extends AbstractController
{
    const UNKNOWN_ERROR = 'Une erreur inconnue est survenue';
    const NOT_CONNECTED = "Vous devez ètre connecté pour poster des commentaires";

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
     * @Route("/trick/{slug}", name="trick.single", methods="GET|POST")
     * @param Trick $trick
     * @return Response
     */
    public function single(Trick $trick, Request $request): Response
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
            }
        }
        return $this->render('trick/single.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/comment/{slug}/{page}", name="trick.comments", methods="GET|POST")
     * @param Trick $trick
     * @return Response
     */
    public function commentByPage(Trick $trick, int $page, Request $request): Response
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
            }
        }
        return $this->render('trick/single.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{slug}", name="trick.edit", methods="GET|POST")
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
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $name = $request->request->get('name');
        if ($name != null) {
            if (true) {
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
