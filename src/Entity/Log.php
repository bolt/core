<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Repository\LogRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'log')]
class Log
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'message', type: 'text')]
    private string $message = '';

    #[ORM\Column(name: 'context', type: 'array', nullable: true)]
    private ?array $context = null;

    #[ORM\Column(name: 'level', type: 'smallint')]
    private int $level = 0;

    #[ORM\Column(name: 'level_name', type: 'string', length: 50)]
    private string $levelName = '';

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(name: 'extra', type: 'array', nullable: true)]
    private ?array $extra = null;

    #[ORM\Column(name: '`user`', type: 'array', nullable: true)]
    private ?array $user = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $content = null;

    #[ORM\Column(name: 'location', type: 'array', nullable: true)]
    private ?array $location = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (array_key_exists('content_id', $this->getContext())) {
            $this->setContent($this->getContext()['content_id']);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getLevelName(): string
    {
        return $this->levelName;
    }

    public function setLevelName(string $levelName): self
    {
        $this->levelName = $levelName;

        return $this;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function setExtra(?array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLocation(): ?array
    {
        return $this->location;
    }

    public function setLocation(?array $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getUser(): ?array
    {
        return $this->user;
    }

    public function setUser(?array $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?int
    {
        return $this->content;
    }

    public function setContent(int $content): self
    {
        $this->content = $content;

        return $this;
    }
}
