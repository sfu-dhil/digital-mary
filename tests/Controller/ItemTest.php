<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ItemTest extends ControllerTestCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_FOUND;

    private const TYPEAHEAD_QUERY = 'name';

    /**
     * @group anon
     * @group index
     */
    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/item/');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group user
     * @group index
     */
    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    /**
     * @group admin
     * @group index
     */
    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/item/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    /**
     * @group anon
     * @group show
     */
    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/item/1');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group user
     * @group show
     */
    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group admin
     * @group show
     */
    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/item/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
    }

    /**
     * @group anon
     * @group typeahead
     */
    public function testAnonTypeahead() : void {
        $this->client->request('GET', '/item/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode((string) $response->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(4, $json);
    }

    /**
     * @group user
     * @group typeahead
     */
    public function testUserTypeahead() : void {
        $this->login(UserFixtures::USER);
        $this->client->request('GET', '/item/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(4, $json);
    }

    /**
     * @group admin
     * @group typeahead
     */
    public function testAdminTypeahead() : void {
        $this->login(UserFixtures::ADMIN);
        $this->client->request('GET', '/item/typeahead?q=' . self::TYPEAHEAD_QUERY);
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $json = json_decode($response->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(4, $json);
    }

    public function testAnonSearch() : void {
        $crawler = $this->client->request('GET', '/item/search');
        $this->assertSame(self::ANON_RESPONSE_CODE, $this->client->getResponse()->getStatusCode());
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'item',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testUserSearch() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'item',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSearch() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/item/search');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'item',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/item/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group edit
     */
    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/1/edit');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/item/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'item[description]' => 'Updated Description',
            'item[inscription]' => 'Updated Inscription',
            'item[translatedInscription]' => 'Updated TranslatedInscription',
            'item[dimensions]' => 'Updated Dimensions',
            'item[references]' => 'Updated Bibliography',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/item/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertSame(1, $responseCrawler->filter('div:contains("Updated Description")')->count());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/item/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/new');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/item/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Create')->form([
            'item[name]' => 'New Name',
            'item[description]' => 'New Description',
            'item[inscription]' => 'New Inscription',
            'item[translatedInscription]' => 'New TranslatedInscription',
            'item[dimensions]' => 'New Dimensions',
            'item[references]' => 'New Bibliography',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertSame(1, $responseCrawler->filter('h1:contains("New Name")')->count());
        $this->assertSame(1, $responseCrawler->filter('div:contains("New Description")')->count());
    }

    /**
     * @group admin
     * @group delete
     */
    public function testAdminDelete() : void {
        $repo = self::getContainer()->get(ItemRepository::class);
        $preCount = count($repo->findAll());

        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/item/1');
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->em->clear();
        $postCount = count($repo->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }

    public function testAnonNewImage() : void {
        $formCrawler = $this->client->request('GET', '/item/1/add_image');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserNewImage() : void {
        $this->login(UserFixtures::USER);
        $formCrawler = $this->client->request('GET', '/item/1/add_image');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNewImage() : void {
        $this->login(UserFixtures::ADMIN);
        $upload = new UploadedFile(__DIR__ . '/../data/35597651312_a188de382c_c.jpg', 'chicken.jpg');

        $formCrawler = $this->client->request('GET', '/item/1/add_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Create')->form([
            'image[imageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/item/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(1, $responseCrawler->filter('div:contains("The image has been added to the item")')->count());

        $this->em->clear();
        $item = $this->em->find(Item::class, 1);
    }

    public function testAnonEditImage() : void {
        $formCrawler = $this->client->request('GET', '/item/1/edit_image/1');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserEditImage() : void {
        $this->login(UserFixtures::ADMIN);
        $upload = new UploadedFile(__DIR__ . '/../data/35597651312_a188de382c_c.jpg', 'chicken.jpg');

        $formCrawler = $this->client->request('GET', '/item/1/add_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Create')->form([
            'image[imageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/item/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(1, $responseCrawler->filter('div:contains("The image has been added to the item")')->count());

        $this->em->clear();
        $item = $this->em->find(Item::class, 1);

        $this->login(UserFixtures::USER);
        $formCrawler = $this->client->request('GET', '/item/1/edit_image/1');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEditImage() : void {
        $this->login(UserFixtures::ADMIN);
        $upload = new UploadedFile(__DIR__ . '/../data/24708385605_c5387e7743_c.jpg', 'cat.jpg');

        $formCrawler = $this->client->request('GET', '/item/1/add_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Create')->form([
            'image[imageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/item/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(1, $responseCrawler->filter('div:contains("The image has been added to the item")')->count());

        $this->em->clear();
        $item = $this->em->find(Item::class, 1);

        $upload = new UploadedFile(__DIR__ . '/../data/32024919067_c2c18aa1c5_c.jpg', 'dog.jpg');
        $formCrawler = $this->client->request('GET', '/item/1/edit_image/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $form = $formCrawler->selectButton('Save')->form([
            'image[newImageFile]' => $upload,
            'image[public]' => 1,
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/item/1');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(1, $responseCrawler->filter('div:contains("The image has been updated")')->count());

        $this->em->clear();
        $item = $this->em->find(Item::class, 1);
    }

    /**
     * @group anon
     * @group edit
     */
    public function testAnonEditRemoteImage() : void {
        $crawler = $this->client->request('GET', '/item/2/edit_remote_image/1');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group edit
     */
    public function testUserEditRemoteImage() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/2/edit_remote_image/1');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group edit
     */
    public function testAdminEditRemoteImage() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/item/2/edit_remote_image/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'remote_image[url]' => 'http://example.com/new/url',
            'remote_image[title]' => 'Updated Title',
            'remote_image[description]' => 'Updated Description',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/item/2');
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group anon
     * @group new
     */
    public function testAnonNewRemoteImage() : void {
        $crawler = $this->client->request('GET', '/item/1/add_remote_image');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @group user
     * @group new
     */
    public function testUserNewRemoteImage() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/item/1/add_remote_image');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group admin
     * @group new
     */
    public function testAdminNewRemoteImage() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/item/1/add_remote_image');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Save')->form([
            'remote_image[url]' => 'http://example.com/new/url',
            'remote_image[title]' => 'New Title',
            'remote_image[description]' => 'New Description',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(7, $responseCrawler->filter('div:contains("New Title")')->count());
    }
}
