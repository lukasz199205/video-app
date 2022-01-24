<?php

namespace App\Tests\Controllers\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerVideoTest extends WebTestCase
{
    public function testNoResults()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
                'query' => 'aaa',
            ]);
        $crawler = $client->submit($form);

        $this->assertStringContainsString('No results were found', $crawler->filter('h1')->text());
    }
}
