<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @var MediaRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(MediaRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/media", name="media", methods="POST")
     * @return Response
     */
    public function list(): Response
    {
        $medias = $this->repository->findAll();
        return $this->render('media/index.html.twig', ['medias' => $medias]);
    }


    /**
     * @Route("/media/add", name="media.add", methods="POST")
     * @return Response
     */
    public function add(Request $request): Response
    {
        return new Response('true');
    }


    /**
     * @Route("/media", name="media.delete", methods="POST")
     * @return Response
     */
    public function media(Request $request): Response
    {
        return new Response('true');
    }
}