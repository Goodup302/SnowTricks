<?php

namespace App\Tests\Service;

use App\Service\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testSlugify() {
        $this->assertStringNotContainsString(" ", Utils::slugify('test test'));
    }
}