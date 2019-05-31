<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TrickRepository;
use App\Service\SendMail;
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
    public function home(AuthorizationCheckerInterface $authChecker, TrickRepository $repository, SendMail $sendMail): Response
    {
        $tricks = $repository->loadMore(0, TrickController::TRICK_PER_LOAD);
        return $this->render('page/home.html.twig', ['tricks' => $tricks]);
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
