<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function test1()
    {
        $product = 1;
        $this->assertSame(1, $product);
    }
}