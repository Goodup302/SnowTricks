<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Repository\ImageRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @var ImageRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $fileUploader;

    public function __construct(ImageRepository $repository, EntityManagerInterface $em, FileUploader $fileUploader)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/image", name="image", methods="POST|GET")
     * @return Response
     */
    public function listImage(): Response
    {
        $images = $this->repository->findAll();
        foreach ($images as $id => $image) {
            $result[$id]['id'] = $image->getId();
            $result[$id]['name'] = $image->getName();
            $result[$id]['url'] = $this->fileUploader->getUploadFolder().$image->getName();
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/image/add/{id}", name="image.add", methods="POST|GET")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function add(Request $request, FileUploader $fileUploader, Trick $trick): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $files = $image->getFiles();
            /** @var Image[] $images */
            $images = array();
            $jsonImage = array();
            foreach ($files as $i => $file){
                $images[$i] = (new Image())->setName($fileUploader->upload($file));
                $this->em->persist($images[$i]);
                $trick->addImage($images[$i]);
            }
            $this->em->flush();
            //
            foreach($images as $image) {
                $jsonImage[] = [
                    'name' => $image->getName(),
                    'id' => $image->getId(),
                    'url' => $this->fileUploader->getUploadFolder().$image->getName(),
                    'thumbnail' => $image->getThumbnail(),
                ];
            }
            return new JsonResponse($jsonImage);
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
        try {
            $fileUploader->delete($image->getName());
            $this->em->remove($image);
            $this->em->flush();
            return new JsonResponse(true);
        } catch (\Exception $e) {
            return new JsonResponse(false);
        }
    }

    /**
     * @Route("/addvideoto/{id}", name="video.add", methods="POST|GET")
     */
    public function addVideoToTrick(Request $request, Trick $trick): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $video->setTrick($trick);
            $this->em->persist($trick);
            $this->em->flush();
            return new JsonResponse([
                'id' => $video->getId(),
                'videoId' => $video->getVideoId(),
                'platform' => $video->getPlatform(),
            ]);
        }
        return new JsonResponse(false);
    }
}