<?php

namespace App\Tests\Controllers\Front;

use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCommentsTest extends WebTestCase
{
    use RoleAdmin;

    public function testNotLoggedInUser()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment'
        ]);
        $client->submit($form);

        $this->assertStringContainsString('Please sign in', $client->getResponse()->getContent());
    }

    public function testNewCommentAndNumberOfComments()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/video-details/16');
        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment'
        ]);

        $this->client->submit($form);

        $this->assertStringContainsString('Test comment', $this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', '/video-list/category/toys,2');

        $this->assertSame('Comments (1)', $crawler->filter('a.ml-1')->text());
    }
}
