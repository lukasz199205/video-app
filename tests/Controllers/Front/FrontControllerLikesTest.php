<?php

namespace App\Tests\Controllers\Front;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerLikesTest extends WebTestCase
{
    use RoleUser;

    public function testLike()
    {
        $this->client->request('POST','/video-list/11/like');
        $crawler = $this->client->request('GET','/video-list/category/movies,4');

        $this->assertSame('(3)', $crawler->filter('small.number-of-likes-11')->text());
    }

    public function testDislike()
    {
        $this->client->request('POST','/video-list/11/dislike');
        $crawler = $this->client->request('GET','/video-list/category/movies,4');

        $this->assertSame('(1)', $crawler->filter('small.number-of-dislikes-11')->text());
    }

    public function testNumberOfLikedVideos1()
    {
        $this->client->request('POST','/video-list/11/like');
        $this->client->request('POST','/video-list/11/like');

        $crawler = $this->client->request('GET','/admin/videos');
        $this->assertEquals(4, $crawler->filter('tr')->count());
    }

    public function testNumberOfLikedVideos2()
    {
        $this->client->request('POST','/video-list/1/unlike');
        $this->client->request('POST','/video-list/12/unlike');

        $crawler = $this->client->request('GET','/admin/videos');
        $this->assertEquals(1, $crawler->filter('tr')->count());
    }
}
