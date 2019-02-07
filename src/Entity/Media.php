<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Upload a JPEG file.")
     * @Assert\File(mimeTypes={ "image/jpeg" })
     */
    private $name;

    private $files;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Trick", mappedBy="images")
     */
    private $tricks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="thumbnail")
     */
    private $thumbnail;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->tricks = new ArrayCollection();
        $this->thumbnail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setFiles($images)
    {
        $this->files = $images;

        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function addFiles($image)
    {
        $this->files[] = $image;
        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->addImage($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            $trick->removeImage($this);
        }

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
/*    public function getThumbnail(): Collection
    {
        return $this->thumbnail;
    }

    public function addThumbnail(Trick $thumbnail): self
    {
        if (!$this->thumbnail->contains($thumbnail)) {
            $this->thumbnail[] = $thumbnail;
            $thumbnail->setThumbnail($this);
        }

        return $this;
    }

    public function removeThumbnail(Trick $thumbnail): self
    {
        if ($this->thumbnail->contains($thumbnail)) {
            $this->thumbnail->removeElement($thumbnail);
            // set the owning side to null (unless already changed)
            if ($thumbnail->getThumbnail() === $this) {
                $thumbnail->setThumbnail(null);
            }
        }

        return $this;
    }*/
}