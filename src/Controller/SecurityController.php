<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Account\ResetPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
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
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    /**
     * SecurityController constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $this->addFlash('error', $authenticationUtils->getLastAuthenticationError());
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $form->getData()->getPassword()));
            $user->createToken();
            //
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', self::REGISTER_SUCCESS);
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/lostpassword", name="lostpassword")
     * @return Response
     */
    public function lostpassword(Request $request): Response
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $form->getData()->getPassword()));
            $user->createToken();
            //
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', self::REGISTER_SUCCESS);
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/resetpassword/{token}", name="resetpassword")
     * @return Response
     */
    public function resetPassword(Request $request, string $token, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $form->getData()->getPassword()));
            $user->createToken();
            //
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', "Votre mot de pass a bien été réinitialiser");
            $this->redirectToRoute('home');
        }
        return $this->render('security/reset.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/activate/{token}", name="activate")
     * @return Response
     */
    public function activate(Request $request, string $token): Response
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->createToken();
            //
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', self::REGISTER_SUCCESS);
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/account", name="account")
     * @return Response
     */
    public function account(Request $request, string $token): Response
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->createToken();
            //
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', self::REGISTER_SUCCESS);
        }
        return $this->redirectToRoute('home');
    }
}
