<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 */
class Figure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", name="groupid")
     */
    private $tag;

    /**
     * @ORM\Column(type="json")
     */
    private $images;

    /**
     * @ORM\Column(type="json")
     */
    private $videos;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publishDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastEdit;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Upload thumbnail as a JPEG file.")
     * @Assert\File(mimeTypes={ "image/jpeg" })
     */
    private $thumbnail;


    public function getThumbnail()
    {
        return $this->thumbnail;
    }
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getTag(): ?int
    {
        return $this->tag;
    }
    public function setTag(int $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getVideos(): ?array
    {
        return $this->videos;
    }
    public function setVideos(array $videos): self
    {
        $this->videos = $videos;

        return $this;
    }

    public function getPublishDate(): ?\DateTime
    {
        return $this->publishDate;
    }
    public function setPublishDate(\DateTime $publishDate): self
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function getLastEdit(): ?\DateTime
    {
        return $this->lastEdit;
    }
    public function setLastEdit(\DateTime $lastEdit): self
    {
        $this->lastEdit = $lastEdit;

        return $this;
    }
}
