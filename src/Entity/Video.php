<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $platform;

    /**
     * @ORM\Column(type="integer")
     */
    private $videoId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlatform(): ?int
    {
        return $this->platform;
    }

    public function setPlatform(int $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getVideoId(): ?int
    {
        return $this->videoId;
    }

    public function setVideoId(int $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }
}
