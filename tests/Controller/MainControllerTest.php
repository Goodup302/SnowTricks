<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testcgu()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/cgu');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }
}