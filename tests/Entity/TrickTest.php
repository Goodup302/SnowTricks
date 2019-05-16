<?php

namespace App\Tests\Entity;

use App\Entity\Trick;
use PHPUnit\Framework\TestCase;

class TrickTest extends TestCase
{
    /*
     * @var Trick
     */
    public $trick;

    public function setUp(): void
    {
        $this->trick = new Trick();
        $this->trick
            ->setPublishDate(new \DateTime())
            ->setLastEdit(new \DateTime());
    }

    public function testIdIsNull() {
        $this->assertNull($this->trick->getId());
    }

    public function testPublishDate() {
        $this->assertLessThan(new \DateTime(), $this->trick->getPublishDate());
    }

    public function testLastEdit() {
        $this->assertLessThan(new \DateTime(), $this->trick->getLastEdit());
        $this->assertGreaterThan($this->trick->getPublishDate(), $this->trick->getLastEdit());
    }
}