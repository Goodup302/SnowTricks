<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    const YOUTUBE_TYPE = 0;
    const DAILYMOTION_TYPE = 1;

    const YOUTUBE = "YouTube";
    const DAILYMOTION = "Dailymotion";

    const ACCEPTS = [Video::YOUTUBE_TYPE, Video::DAILYMOTION_TYPE];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices=Video::ACCEPTS)
     * @Assert\NotBlank()
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $videoId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="videos")
     */
    private $trick;

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

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
