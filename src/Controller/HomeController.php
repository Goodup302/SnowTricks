<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function home(FigureRepository $repository, EntityManagerInterface $em): Response
    {
        return $this->render('figure/index.html.twig', ['figures' => $repository->findAll()]);
    }
}