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
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function add(Request $request, FileUploader $fileUploader): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);
        if (true) {
            $files = $media->getFiles();
            $uploadMedia = array();
            foreach ($files as $i => $file){
                $uploadMedia[$i] = new Media();
                $uploadedFile = $fileUploader->upload($file);
                $uploadMedia[$i]->setName($uploadedFile);
                $this->em->persist($uploadMedia[$i]);
            }
            $this->em->flush();
            return $this->render('media/item.html.twig', ['medias' => $uploadMedia]);
        }
        return new Response(json_encode(false));
    }

    /**
     * @Route("/media/delete/{id}", name="media.delete", methods="DELETE")
     * @param Media $media
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function media(Media $media, FileUploader $fileUploader): Response
    {
        $fileUploader->delete($media->getName());
        $this->em->remove($media);
        $this->em->flush();
        return new Response(json_encode(true));
    }
}