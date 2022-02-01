<?php

namespace App\Listeners;

use App\Entity\User;
use App\Entity\Video;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class NewVideoListener
{
    public function __construct(\Twig\Environment $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Video)
        {
            return;
        }

        $entityManager = $args->getObjectManager();

        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->templating->render(
                        'emails/new_video.html.twig',
                        [
                            'name' => $user->getName(),
                            'video' => $entity
                        ]
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }
    }
}