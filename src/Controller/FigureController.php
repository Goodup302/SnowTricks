<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Service\FileUploader;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    /**
     * @var FigureRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(FigureRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/figure/{id}", name="figure.single", methods="GET")
     * @param Figure $figure
     * @return Response
     */
    public function single(Figure $figure): Response
    {
        return $this->render('figure/single.html.twig', ['figure' => $figure]);
    }

    /**
     * @Route("/edit/{id}", name="figure.edit", methods="GET|POST")
     * @param Figure $figure
     * @param Request $request
     * @return Response
     */
    public function edit(Figure $figure, Request $request): Response
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
        return $this->render('figure/edit.html.twig', ['form' => $form->createView(), 'figure' => $figure]);
    }

    /**
     * @Route("/new", name="figure.new", methods="GET|POST")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Upload Thumbnail
            $uploadedFile = $fileUploader->upload($figure->getThumbnail());
            $figure->setThumbnail($uploadedFile);
            //Upload Media
            $figure = $form->getData();
            $figure->setImages(array());
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
        return $this->render('figure/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="figure.delete", methods="DELETE")
     * @param Figure $figure
     * @return Response
     */
    public function delete(Figure $figure): Response
    {
        $this->em->remove($figure);
        $this->em->flush();
        return $this->redirectToRoute('home');
    }
}
