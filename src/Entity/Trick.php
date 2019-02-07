<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 */
class Trick
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

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Media", inversedBy="tricks")
     */
    private $images;

    public function __construct()
    {
        //$this->images = new ArrayCollection();
    }

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

    /**
     * @return Collection|Media[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Media $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Media $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }
}
