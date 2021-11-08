<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Bolt\Repository\UserAuthTokenRepository")
 */
class UserAuthToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="Bolt\Entity\User", inversedBy="userAuthTokens", cascade={"persist"}) */
    private $user;

    /** @ORM\Column(type="string", length=255) */
    private $useragent;

    /** @ORM\Column(type="datetime") */
    private $validity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUseragent(): ?string
    {
        return $this->useragent;
    }

    public function setUseragent(string $useragent): self
    {
        $this->useragent = $useragent;

        return $this;
    }

    public function getValidity(): ?\DateTimeInterface
    {
        return $this->validity;
    }

    public function setValidity(\DateTimeInterface $validity): self
    {
        $this->validity = $validity;

        return $this;
    }
}
