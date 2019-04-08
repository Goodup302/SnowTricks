<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            return $this->render('page/home.html.twig', ['tricks' => $repository->findAll()]);
        }
        return $this->render('page/home.html.twig', ['tricks' => $repository->getVisible()]);
    }

    /**
     * @Route("/cgu", name="cgu")
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function cgu(): Response
    {
        return $this->render('page/cgu.html.twig');
    }
}
