<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 * @ORM\Table(name="subscriptions")
 */
class Subscription
{
    private static $planDataNames = ['free','pro','enterprise'];

    private static $planDataPrices = [
        'free' => 0,
        'pro' => 15,
        'enterprise' => 29
    ];

    private static function getPlanNameByIndex(int $index): string
    {
        return self::$planDataNames[$index];
    }

    private static function getPlanDataPriceByName(string $name): int
    {
        return self::$planDataPrices[$name];
    }

    private static function getPlanDataNames(): array
    {
        return self::$planDataNames;
    }

    private static function getPlanDataPrices(): array
    {
        return self::$planDataPrices;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $validTo;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $paymentStatus;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $freePlanUsed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getFreePlanUsed(): ?bool
    {
        return $this->freePlanUsed;
    }

    public function setFreePlanUsed(?bool $freePlanUsed): self
    {
        $this->freePlanUsed = $freePlanUsed;

        return $this;
    }
}
