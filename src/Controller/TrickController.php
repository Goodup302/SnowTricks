<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\FigureType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Service\GenerateData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
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
        return $this->render('trick/index.html.twig', ['figures' => $repository->findAll()]);
    }

    /**
     * @Route("/figure/{id}", name="figure.single", methods="GET")
     * @param Trick $trick
     * @return Response
     */
    public function single(Trick $trick): Response
    {
        return $this->render('trick/single.html.twig', ['figure' => $trick]);
    }

    /**
     * @Route("/edit/{id}", name="figure.edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request): Response
    {
        $form = $this->createForm(FigureType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $trick->setLastEdit($date);
            $this->em->flush();
        }
        dump($form->createView()->vars["value"]);
        return $this->render('trick/edit.html.twig', ['form' => $form->createView(), 'figure' => $trick]);
    }

    /**
     * @Route("/new", name="figure.new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(FigureType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
/*            //Upload Thumbnail
            $uploadedFile = $fileUploader->upload($trick->getThumbnail());
            $trick->setThumbnail($uploadedFile);*/
            //Upload Media
            $trick = $form->getData();
            $trick->setVideos(array());
            //Publish Date
            //date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $trick->setPublishDate($date);

            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Figure ajoutée avec succès');

            return $this->redirectToRoute("figure.edit", ['id' => $trick->getId()]);
        }
        return $this->render('trick/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="figure.delete", methods="DELETE")
     * @param Trick $trick
     * @return Response
     */
    public function delete(Trick $trick): Response
    {
        $this->em->remove($trick);
        $this->em->flush();
        return $this->redirectToRoute('home');
    }
}
