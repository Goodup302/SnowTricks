<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\GenerateData;
use Doctrine\ORM\EntityManagerInterface;
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
    public function home(TrickRepository $repository, GenerateData $data): Response
    {
        $data->add(0);
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
        //var_dump($trick->getThumbnail()->relation());

        //Upload Image form
        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);
        //Trick form
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //date_default_timezone_set('Europe/Paris');
            /** @var Trick $trick */
            $trick = $form->getData();
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $trick->setLastEdit($date);
            $this->em->persist($trick);
            $this->em->flush();
            return $this->redirectToRoute('trick.single', ['slug' => $trick->getSlug()]);
        }
        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'imageForm' =>$imageForm->createView(),
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
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $trick = $form->getData();

            //Publish Date
            //date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $trick->setPublishDate($date);

            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Figure ajoutée avec succès');

            return $this->redirectToRoute("trick.edit", ['id' => $trick->getId()]);
        }
        return $this->render('trick/edit.html.twig', ['form' => $form->createView()]);
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
            ));
        }
    }

    /**
     * @Route("/add_tag", name="add.tag", methods="POST")
     * @param Trick $trick
     * @return Response
     */
    public function addTag(Trick $trick): Response
    {
        foreach ($trick->getComments() as $comment) {
            $this->em->remove($comment);
        }
        $this->em->remove($trick);
        $this->em->flush();
        return $this->redirectToRoute('home');
    }
}
