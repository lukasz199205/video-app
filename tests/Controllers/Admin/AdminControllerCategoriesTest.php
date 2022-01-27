<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\Category;
use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerCategoriesTest extends WebTestCase
{
    use RoleAdmin;

    public function testTextOnPage()
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems()
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testNewCategory()
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'Other electronics'
        ]);
        $this->client->submit($form);

        $category = $this->entityManager->getRepository(Category::class)
            ->findOneBy(['name' => 'Other electronics']);

        $this->assertNotNull($category);
        $this->assertSame('Other electronics', $category->getName());
    }

    public function testEditCategory()
    {
        $crawler = $this->client->request('GET','/admin/su/edit-category/1');
        $form = $crawler->selectButton('Save')->form([
            'category[parent]' => 0,
            'category[name]' => 'Electronics 2'
        ]);
        $this->client->submit($form);

        $category = $this->entityManager->getRepository(Category::class)
            ->find(1);

        $this->assertSame('Electronics 2', $category->getName());

    }

    public function testDeleteCategory()
    {
        $crawler = $this->client->request('GET','/admin/su/delete-category/1');

        $category = $this->entityManager->getRepository(Category::class)
            ->find(1);
        $this->assertNull($category);
    }
}

