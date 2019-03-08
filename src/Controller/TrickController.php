<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\ImageType;
use App\Form\TagType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\TrickRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    const UNKNOWN_ERROR = 'Une erreur inconnue est survenue';

    /**
     * @var TrickRepository
     */
    private $repository;

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
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     * @param TrickRepository $repository
     * @return Response
     */
    public function home(TrickRepository $repository): Response
    {
        return $this->render('index.html.twig', ['tricks' => $repository->findAll()]);
    }

    /**
     * @Route("/trick/{slug}", name="trick.single", methods="GET")
     * @param Trick $trick
     * @return Response
     */
    public function single(Trick $trick): Response
    {
        return $this->render('trick/single.html.twig', ['trick' => $trick]);
    }

    /**
     * @Route("/edit/{id}", name="trick.edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request): Response
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
            if (is_null($trick->getPublishDate())) {
                $trick->setPublishDate(Utils::getCurrentDateTime());
                $trick->setSlug(Utils::slugify($trick->getName()));
            } else {
                $trick->setLastEdit(Utils::getCurrentDateTime());
            }
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
            $trick = new Trick();
            $trick->setName($name);
            $this->em->persist($trick);
            $this->em->flush();
            return $this->redirectToRoute("trick.edit", ['id' => $trick->getId()]);
        } else {
            $this->addFlash('error', 'Le nom saisi est déjà utilisé !');
            return $this->redirectToRoute("home");
        }

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
     * @Route("/add_tag", name="add.tag", methods="POST")
     * @param Trick $trick
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
