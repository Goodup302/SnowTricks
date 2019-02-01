<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use App\Service\GenerateData;
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
    public function home(FigureRepository $repository, EntityManagerInterface $em, GenerateData $data): Response
    {
        //$data->add();
        return $this->render('figure/index.html.twig', ['figures' => $repository->findAll()]);
    }
}