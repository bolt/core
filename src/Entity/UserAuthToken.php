<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Repository\UserAuthTokenRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAuthTokenRepository::class)]
class UserAuthToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'userAuthTokens')]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $useragent = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $validity;

    public function __construct()
    {
        // Default value, should be overridden in application code
        $this->validity = new DateTime();
    }

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

    public function getUseragent(): string
    {
        return $this->useragent;
    }

    public function setUseragent(string $useragent): self
    {
        $this->useragent = $useragent;

        return $this;
    }

    public function getValidity(): ?DateTimeInterface
    {
        return $this->validity;
    }

    public function setValidity(DateTimeInterface $validity): self
    {
        $this->validity = $validity;

        return $this;
    }
}
