<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class SendMail
{
    private $twig;
    private $mailer;
    private $params;

    public function __construct(Environment $twig, \Swift_Mailer $mailer, ParameterBagInterface $params)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->params = $params;
        //dump($this->generateUrl('home'));
    }

    public function sendForgot() {
/*        $view = $this->twig->render('email/forgot.html.twig', [
            'url' => $this->generateUrl('resetpassword', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'user' => $user
        ]);
        $mail = (new \Swift_Message('Récupération du mot de passe'))
            ->setSender($this->params->get('sender_email'))
            ->setFrom($this->params->get('sender_email'))
            ->setTo($user->getEmail())
            ->setBody($view, 'text/html')
        ;
        $this->mailer->send($mail);*/
    }

    public function sendConfirm() {
/*        $view = $this->twig->render('email/confirm.html.twig', [
            'url' => $this->generateUrl('activate', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'user' => $user
        ]);
        $mail = (new \Swift_Message('Activation'))
            ->setSender($this->params->get('sender_email'))
            ->setFrom($this->params->get('sender_email'))
            ->setTo($user->getEmail())
            ->setBody($view, 'text/html')
        ;
        $this->mailer->send($mail);*/
    }
}