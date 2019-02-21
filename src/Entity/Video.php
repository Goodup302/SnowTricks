<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    const YOUTUBE_TYPE = 0;
    const DAILYMOTION_TYPE = 1;


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
     * @ORM\Column(type="string", length=255)
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

    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    public function setVideoId(string $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }
}
