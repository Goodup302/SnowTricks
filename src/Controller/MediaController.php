<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use App\Service\FileUploader;
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
     * @Route("/media", name="media", methods="POST|GET")
     * @return Response
     */
    public function list(): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $medias = $this->repository->findAll();
        return $this->render('media/index.html.twig', ['medias' => $medias, 'form' => $form->createView()]);
    }


    /**
     * @Route("/media/add", name="media.add", methods="POST|GET")
     * @return Response
     */
    public function add(Request $request, FileUploader $fileUploader): Response
    {
        dump($request);
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Upload Thumbnail
            $uploadedFile = $fileUploader->upload($media->getName());
            $media->setName($uploadedFile);
            $this->em->persist($media);
            $this->em->flush();
            return $this->render('test.html.twig', ['form' => $form->createView()]);
        }
        return $this->render('test.html.twig', ['form' => $form->createView()]);
        return new Response($form->getErrors());
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