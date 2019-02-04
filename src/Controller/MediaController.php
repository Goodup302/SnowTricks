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
        var_dump($media);
        if (true) {
            $files = $media->getFiles();

            foreach ($files as $file){
                $_media = new Media();
                $uploadedFile = $fileUploader->upload($file);
                $_media->setName($uploadedFile);
                var_dump($_media);
                $this->em->persist($_media);
            }

            //Upload Thumbnail
            $this->em->flush();

            $result = array(
                'path' => $fileUploader->getPath($uploadedFile),
            );
            return new Response(json_encode(true));
        }
        return new Response(json_encode(false));
    }

    /**
     * @Route("/media/delete/{id}", name="media.delete", methods="DELETE")
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