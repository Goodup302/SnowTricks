<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\ImageType;
use App\Form\TagType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\TrickRepository;
use App\Service\Date;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\ka_GE\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function home(AuthorizationCheckerInterface $authChecker, TrickRepository $repository): Response
    {
        if ($authChecker->isGranted(User::ROLE_AMDIN)) {
            return $this->render('index.html.twig', ['tricks' => $repository->findAll()]);
        }
        return $this->render('index.html.twig', ['tricks' => $repository->getAll()]);
    }

    /**
     * @Route("/cgu", name="cgu")
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function cgu(): Response
    {
        return $this->render('cgu.html.twig');
    }
}