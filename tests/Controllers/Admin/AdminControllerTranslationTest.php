<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTranslationTest extends WebTestCase
{
    use RoleUser;

    public function testTranslations()
    {
        $this->client->request('GET', '/pl/admin/');

        $this->assertStringContainsString( 'Mój profil', $this->client->getResponse()->getContent() );

        $this->assertStringContainsString('lista-video', $this->client->getResponse()->getContent());
    }
}
