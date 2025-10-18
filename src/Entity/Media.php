<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Repository\MediaRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Path;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 191)]
    private string $location = '';

    #[ORM\Column(type: 'text', length: 1000)]
    private string $path = '';

    #[ORM\Column(type: 'string', length: 191)]
    private string $filename = '';

    #[ORM\Column(type: 'string', length: 191)]
    private string $type = '';

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $width = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $height = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $filesize = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cropX = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cropY = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $cropZoom = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $author = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $modifiedAt;

    #[ORM\Column(type: 'string', length: 191, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $originalFilename = null;

    #[ORM\Column(type: 'string', length: 191, nullable: true)]
    private ?string $copyright = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->modifiedAt = $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilenamePath(): string
    {
        if (empty($this->path)) {
            $path = $this->filename;
        } else {
            $path = Path::canonicalize($this->path . '/' . $this->filename);
        }

        return $path;
    }

    public function setPath(string $path): self
    {
        if (mb_strpos($path, '/') === 0) {
            $path = mb_substr($path, 1);
        }

        $this->path = $path;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if ($type === 'jpeg') {
            $type = 'jpg';
        }

        $this->type = $type;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    public function setFilesize(int $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(?string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): self
    {
        $this->copyright = $copyright;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCropX(): ?int
    {
        return $this->cropX;
    }

    public function setCropX(int $cropX): self
    {
        $this->cropX = $cropX;

        return $this;
    }

    public function getCropY(): ?int
    {
        return $this->cropY;
    }

    public function setCropY(int $cropY): self
    {
        $this->cropY = $cropY;

        return $this;
    }

    public function getCropZoom(): ?float
    {
        return $this->cropZoom;
    }

    public function setCropZoom(float $cropZoom): self
    {
        $this->cropZoom = $cropZoom;

        return $this;
    }
}
