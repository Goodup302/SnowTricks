<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    const REGISTER_SUCCESS = "Le compte vient d'ètre créé avec succès, nous vous invitons à l'activer via le mail qui vous a été envoyé.";

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TrickController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="register")
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return Response
     */
    public function register(UserPasswordEncoderInterface $encoder, Request $request): Response
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($encoder->encodePassword($user, $form->getData()->getPassword()));
            $user->createToken();
            //
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', self::REGISTER_SUCCESS);
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }
}
