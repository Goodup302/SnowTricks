<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\Account\EditAccountType;
use App\Form\Account\ResetPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SecurityController extends AbstractController
{
    const REGISTER_SUCCESS = "Le compte vient d'ètre créé avec succès, nous vous invitons à l'activer via le mail qui vous a été envoyé.";
    const RESET_SUCCESS = "Votre mot de passe à bien été réinitialisé";
    const ACTIVATE_SUCCESS = "Votre compte vient d'être activé";
    const FORGOT_SUCCESS = "Un mail de récupération de mot de passe vient de vous être envoyé";
    const FORGOT_ERROR = "Ce pseudo est introuvable";
    const PROFILE_UPDATE_SUCCESS = "Votre profil a bien été mise a jour";

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
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request, SendMail $sendMail): Response
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
            $sendMail->sendConfirm($user);
            return $this->redirectToRoute('login');
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/forgotpassword", name="forgotpassword")
     * @return Response
     */
    public function forgotpassword(Request $request, UserRepository $userRepository, SendMail $sendMail): Response
    {
        $username =$request->request->get('username');
        if ($username) {
            /** @var User $user */
            $user = $userRepository->findOneBy(['username' => $username]);;
            if ($user) {
                $this->addFlash('success', self::FORGOT_SUCCESS);
                $sendMail->sendForgot($user);
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
     * @Security("is_granted('ROLE_ADMIN')")
     * @return Response
     */
    public function account(Request $request, FileUploader $fileUploader): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $files = new Image();
        $form = $this->createForm(EditAccountType::class, $files);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $files->getSingleFile();
            if ($imageFile != null) {
                $image = (new Image())
                    ->setName($fileUploader->upload($imageFile))
                    ->setAlt($imageFile->getClientOriginalName())
                ;
                $user->setProfileImage($image);
                //
                $this->em->persist($image);
                $this->em->persist($user);
                $this->em->flush();
                //
                $this->addFlash('success', self::PROFILE_UPDATE_SUCCESS);
            }
        }
        return $this->render('security/account.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
