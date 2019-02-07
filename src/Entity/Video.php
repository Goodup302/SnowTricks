<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    const LIST = array(
        "youtube" => 0,
        "dailymotion" => 1,
        "facebook" => 2,
        "twitter" => 3
    );

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $videoId;

    /**
     * @ORM\Column(type="integer")
     */
    private $platform;

    /**
     * @return integer
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param integer $platform
     * @return Video
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * @param string $videoId
     * @return Video
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        return $this;
    }
}