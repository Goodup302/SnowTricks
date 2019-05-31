<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class SendMail
{
    private $twig;
    private $mailer;
    private $params;
    private $urlGenerator;

    public function __construct(Environment $twig, \Swift_Mailer $mailer, ParameterBagInterface $params,  UrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->params = $params;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendForgot(User $user): bool
    {
        try {
            $view = $this->twig->render('email/forgot.html.twig', [
                'url' => $this->urlGenerator->generate('resetpassword', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                'user' => $user
            ]);
            $mail = (new \Swift_Message('Récupération du mot de passe'))
                ->setSender($this->params->get('sender_email'))
                ->setFrom($this->params->get('sender_email'))
                ->setTo($user->getEmail())
                ->setBody($view, 'text/html')
            ;
            return $this->mailer->send($mail);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendConfirm(User $user): bool
    {
        try {
            $view = $this->twig->render('email/confirm.html.twig', [
                'url' => $this->urlGenerator->generate('activate', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                'user' => $user
            ]);
            $mail = (new \Swift_Message('Activation'))
                ->setSender($this->params->get('sender_email'))
                ->setFrom($this->params->get('sender_email'))
                ->setTo($user->getEmail())
                ->setBody($view, 'text/html')
            ;
            return $this->mailer->send($mail);
        } catch (\Exception $e) {
            return false;
        }
    }
}