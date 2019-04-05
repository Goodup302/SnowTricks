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
    const LOGIN_ERROR = "Identifiant invalides";
    const RESET_SUCCESS = "Votre mot de pass à bien été réinitialisé";
    const ACTIVATE_SUCCESS = "Votre compte vient d'ètre activé";
    const FORGOT_SUCCESS = "Un mail de récupération de mot de pass vient de vous ètre envoyé";
    const FORGOT_ERROR = "Ce pseudo est introuvable";

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
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) $this->addFlash('failed', self::LOGIN_ERROR);
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request, \Swift_Mailer $mailer): Response
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
            //
            $mail = (new \Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo('contact@snowtricks.fr')
                ->setBody(
                    $this->renderView('email/confirm.html.twig', [
                        'url' => 'test',
                        'user' => $user,
                    ]),'text/html')
            ;
            $mailer->send($mail);
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/mail", name="mail")
     */
    public function mail(\Swift_Mailer $mailer): Response
    {
        $mail = (new \Swift_Message('Hello Email'))
            ->setSender('contact@snowtricks.fr')
            ->setFrom('contact@snowtricks.fr')
            ->setTo('j.f0471430704@gmail.com')
            ->setBody(
                $this->renderView(
                    'email/confirm.html.twig',
                    ['url' => 'test', 'user' => $this->getUser()]
                ),'text/html')
        ;
        $mailer->send($mail);
        return $this->render("email/confirm.html.twig", [
            'user' => $this->getUser(),
            'url' => 'google.fr',
        ]);
    }

    /**
     * @Route("/forgotpassword", name="forgotpassword")
     * @return Response
     */
    public function forgotpassword(Request $request, UserRepository $userRepository): Response
    {
        $username =$request->request->get('username');
        if ($username) {
            /** @var User $user */
            $user = $userRepository->findOneBy(['username' => $username]);;
            if ($user) {
                //Add send mail
                $this->addFlash('success', self::FORGOT_SUCCESS);
            } else {
                $this->addFlash('failed', self::FORGOT_ERROR);
            }
        }
        return $this->render('security/forgot.html.twig');
    }

    /**
     * @Route("/resetpassword/{token}", name="resetpassword")
     * @return Response
     */
    public function resetPassword(Request $request, string $token, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if ($user) {
            dump($user);
            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $form->getData()->getPassword();
                $user->setPassword($this->encoder->encodePassword($user, $password));
                $user->setActivate(true);
                $user->createToken();
                //
                $this->em->persist($user);
                $this->em->flush();
                //
                $this->addFlash('success', self::RESET_SUCCESS);
                return $this->redirectToRoute('login');
            }
            return $this->render('security/reset.html.twig', ['form' => $form->createView()]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/activate/{token}", name="activate")
     * @return Response
     */
    public function activate(string $token, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['token' => $token]);
        if ($user && !$user->getActivate()) {
            dump($user);
            $user->setActivate(true)->createToken();
            $this->em->persist($user);
            $this->em->flush();
            //
            $this->addFlash('success', self::ACTIVATE_SUCCESS);
            return $this->redirectToRoute('login');
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/account", name="account")
     * @return Response
     */
    public function account(Request $request): Response
    {
        return $this->redirectToRoute('home');
    }
}
