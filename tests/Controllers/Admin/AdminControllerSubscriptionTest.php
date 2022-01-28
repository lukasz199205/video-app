<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerSubscriptionTest extends WebTestCase
{
    use RoleUser;

    public function testDeleteSubscription()
    {
        $crawler = $this->client->request('GET','/admin/');
        $link = $crawler->filter('a:contains("cancel plan")')->link();
        $this->client->click($link);
        $this->client->request('GET', '/video-list/category/toys,2');

        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }
}

