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
use PhpParser\Node\Scalar\String_;
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
     * @Route("/", name="home")
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function home(AuthorizationCheckerInterface $authChecker): Response
    {
        if ($authChecker->isGranted(User::ROLE_AMDIN)) {
            return $this->render('index.html.twig', ['tricks' => $this->trickRepository->findAll()]);
        }
        return $this->render('index.html.twig', ['tricks' => $this->trickRepository->getAll()]);
    }

    /**
     * @Route("/trick/{slug}", name="trick.single", methods="GET|POST")
     * @param Trick $trick
     * @return Response
     */
    public function single(Trick $trick, Request $request, Date $date): Response
    {
        $user = $this->getUser();
        dump($user);
        if ($trick->isCreated() || $this->isGranted(User::ROLE_AMDIN)) {
            //FORM
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($user != null) {
                    $comment
                        ->setTrick($trick)
                        ->setUser($user)
                        ->setPublishDate($date->currentDateTime());
                    $this->em->persist($comment);
                    $this->em->flush();
                } else {
                    $this->addFlash('error', self::NOT_CONNECTED);
                }
            }
            return $this->render('trick/single.html.twig', [
                'trick' => $trick,
                'commentForm' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/edit/{slug}", name="trick.edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request, Date $date): Response
    {
        //Upload Video form
        $video = new Video();
        $videoOption = [
            'attr' => [
                'action' => $this->generateUrl('video.add', ['id' => $trick->getId()])
            ]
        ];
        $videoForm = $this->createForm(VideoType::class, $video, $videoOption);
        //Upload Image form
        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);
        //Trick form
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Trick $trick */
            $trick = $form->getData();
            if ($trick->isCreated()) {
                $trick->setLastEdit($date->currentDateTime());
            } else {
                $trick->setPublishDate($date->currentDateTime());
            }
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
     * @Route("/delete/{id}", name="trick.delete") //methods="DELETE"
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
