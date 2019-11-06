<?php

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

    /**
     * @ORM\OneToOne(targetEntity="Bolt\Entity\User", inversedBy="userAuthToken", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $useragent;

    /**
     * @ORM\Column(type="datetime")
     */
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

    public static function factory(User $user, string $useragent, \DateTime $validity): self
    {
        $userAuthToken = new self();

        $userAuthToken->setUser($user);
        $userAuthToken->setUseragent($useragent);
        $userAuthToken->setValidity($validity);

        return $userAuthToken;
    }
}
