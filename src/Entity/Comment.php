<?php

namespace App\Entity;

use App\Service\FileUploader;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publishDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="comments")
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishDate(): ?\DateTimeInterface
    {
        return $this->publishDate;
    }

    public function setPublishDate(\DateTimeInterface $publishDate): self
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
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

/*    public function toArray(FileUploader $fileUploader, \Symfony\Component\Asset\Packages $assets)
    {
        if ($this->getUser()->getProfileImage() != null) {
            $name = $this->getUser()->getProfileImage()->getName();
            $folder = $fileUploader->getUploadFolder();
            $image = $assets->getUrl($folder.$name);
        } else {
            $image = $assets->getUrl("/image/default_user.jpg");
        }
        $result = [
            'id' => $this->getId(),
            'image' => $image,
            'username' => $this->getUser()->getUsername(),
            'content' => $this->getContent(),
            'date' => $this->getPublishDate()->format("Y-m-d H:i"),
        ];
        return $result;
    }*/
}
