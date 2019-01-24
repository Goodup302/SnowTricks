<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
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
     * @return Response
     */
    public function single(Figure $figure, Request $request): Response
    {
        return $this->render('figure/index.html.twig', ['figure' => $figure]);
    }

    /**
     * @Route("/edit/{id}", name="figure.edit", methods="GET|POST")
     * @return Response
     */
    public function edit(Figure $figure, Request $request): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        return $this->render('figure/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/new", name="figure.new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure = $form->getData();
            $figure->setImages(array());
            $figure->setVideos(array());

            date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');

            $figure->setLastEdit($date);
            $figure->setPublishDate($date);

            $this->em->persist($figure);
            $this->em->flush();
            dump($figure);
            $this->addFlash('success', 'Figure ajoutée avec succès');
            return $this->redirectToRoute("figure.edit", ['id' => $figure->getId()]);
        }
        return $this->render('figure/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="figure.delete", methods="DELETE")
     * @return Response
     */
    public function delete(Figure $figure): Response
    {
        $this->em->remove($figure);
        $this->em->flush();
        return $this->redirectToRoute('home');
    }
}