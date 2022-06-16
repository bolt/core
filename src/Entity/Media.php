<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Path;

/**
 * @ORM\Entity(repositoryClass="Bolt\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=191) */
    private $location;

    /** @ORM\Column(type="text", length=1000) */
    private $path;

    /** @ORM\Column(type="string", length=191) */
    private $filename;

    /** @ORM\Column(type="string", length=191) */
    private $type;

    /** @ORM\Column(type="integer", nullable=true) */
    private $width;

    /** @ORM\Column(type="integer", nullable=true) */
    private $height;

    /** @ORM\Column(type="integer", nullable=true) */
    private $filesize;

    /** @ORM\Column(type="integer", nullable=true) */
    private $cropX;

    /** @ORM\Column(type="integer", nullable=true) */
    private $cropY;

    /** @ORM\Column(type="float", nullable=true) */
    private $cropZoom;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /** @ORM\Column(type="datetime") */
    private $createdAt;

    /** @ORM\Column(type="datetime") */
    private $modifiedAt;

    /** @ORM\Column(type="string", length=191, nullable=true) */
    private $title;

    /** @ORM\Column(type="string", length=1000, nullable=true) */
    private $description;

    /** @ORM\Column(type="string", length=1000, nullable=true) */
    private $originalFilename;

    /** @ORM\Column(type="string", length=191, nullable=true) */
    private $copyright;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPath(): ?string
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

    public function getType(): ?string
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeInterface $modifiedAt): self
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

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCropX()
    {
        return $this->cropX;
    }

    public function setCropX(int $cropX): self
    {
        $this->cropX = $cropX;

        return $this;
    }

    public function getCropY()
    {
        return $this->cropY;
    }

    public function setCropY(int $cropY): self
    {
        $this->cropY = $cropY;

        return $this;
    }

    public function getCropZoom()
    {
        return $this->cropZoom;
    }

    public function setCropZoom(float $cropZoom): self
    {
        $this->cropZoom = $cropZoom;

        return $this;
    }
}
