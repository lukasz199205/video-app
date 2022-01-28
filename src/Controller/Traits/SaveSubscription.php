<?php

namespace App\Controller\Traits;

use App\Entity\Subscription;

trait SaveSubscription
{
    private function saveSubscription($plan, $user)
    {
        $date = new \DateTime();
        $date->modify('+1 month');
        $subscription = $user->getSubscription();

        if (null === $subscription) {

            $subscription = new Subscription();
        }

        if ($subscription->getFreePlanUsed() && $plan == Subscription::getPlanDataNameByIndex(0)) {

            return;
        }

        $subscription->setValidTo($date);
        $subscription->setPlan($plan);

        if ($plan == Subscription::getPlanDataNameByIndex(0)) {

            $subscription->setFreePlanUsed(true);
            $subscription->setPaymentStatus('paid');
        }

        $subscription->setPaymentStatus('paid'); //temporary

        $user->setSubscription($subscription);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }
}