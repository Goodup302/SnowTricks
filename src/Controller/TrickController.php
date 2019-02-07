<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\FigureType;
use App\Repository\MediaRepository;
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
     * @param Trick $figure
     * @return Response
     */
    public function single(Trick $figure): Response
    {
        return $this->render('trick/single.html.twig', ['figure' => $figure]);
    }

    /**
     * @Route("/edit/{id}", name="figure.edit", methods="GET|POST")
     * @param Trick $figure
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $figure, Request $request): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $figure->setLastEdit($date);
            $this->em->flush();
        }
        dump($form->createView()->vars["value"]);
        return $this->render('trick/edit.html.twig', ['form' => $form->createView(), 'figure' => $figure]);
    }

    /**
     * @Route("/new", name="figure.new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $figure = new Trick();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
/*            //Upload Thumbnail
            $uploadedFile = $fileUploader->upload($figure->getThumbnail());
            $figure->setThumbnail($uploadedFile);*/
            //Upload Media
            $figure = $form->getData();
            $figure->setVideos(array());
            //Publish Date
            //date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $figure->setPublishDate($date);

            $this->em->persist($figure);
            $this->em->flush();
            $this->addFlash('success', 'Figure ajoutée avec succès');

            return $this->redirectToRoute("figure.edit", ['id' => $figure->getId()]);
        }
        return $this->render('trick/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="figure.delete", methods="DELETE")
     * @param Trick $figure
     * @return Response
     */
    public function delete(Trick $figure): Response
    {
        $this->em->remove($figure);
        $this->em->flush();
        return $this->redirectToRoute('home');
    }
}
