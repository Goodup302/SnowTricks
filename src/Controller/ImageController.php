<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @var ImageRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ImageRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/image", name="image", methods="POST|GET")
     * @return Response
     */
    public function list(): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $images = $this->repository->findAll();
        return $this->render('media/index.html.twig', ['medias' => $images, 'form' => $form->createView()]);
    }


    /**
     * @Route("/image/add", name="image.add", methods="POST|GET")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function add(Request $request, FileUploader $fileUploader): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if (true) {
            $files = $image->getFiles();
            $uploadMedia = array();
            foreach ($files as $i => $file){
                $uploadMedia[$i] = new Image();
                $uploadedFile = $fileUploader->upload($file);
                $uploadMedia[$i]->setName($uploadedFile);
                $this->em->persist($uploadMedia[$i]);
            }
            $this->em->flush();
            return $this->render('media/item.html.twig', ['medias' => $uploadMedia]);
        }
        return new JsonResponse(false);
    }

    /**
     * @Route("/image/delete/{id}", name="image.delete", methods="DELETE")
     * @param Image $image
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function delete(Image $image, FileUploader $fileUploader): Response
    {
        $fileUploader->delete($image->getName());
        $this->em->remove($image);
        $this->em->flush();
        return new JsonResponse(true);
    }
}