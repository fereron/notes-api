<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NotesControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /** @test */
    public function unauthenticated_user_cant_see_notes_list()
    {
        $this->client->request('GET', '/api/notes');
        $response = $this->client->getResponse();

        $this->assertEquals(401 , $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    /** @test */
    public function authenticated_user_can_see_notes_list()
    {
        $token = $this->createUser();

        $this->client->request(
            'GET', '/api/notes', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($response->getContent());
    }

    /** @test */
    public function user_can_create_note()
    {
        $token = $this->createUser();

        $params = [
            'title' => 'New test title',
            'body' => 'Test body',
        ];

        $this->client->request(
            'POST', '/api/notes/create', $params, [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $this->client->request(
            'GET', '/api/notes/4', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $note = json_decode($response->getContent(), true)['note'];

        $this->assertEquals($params['title'], $note['title']);
    }

    /** @test */
    public function user_can_create_post_with_image()
    {
        $token = $this->createUser();

        $file = tempnam(sys_get_temp_dir(), 'upl'); // create file
        imagepng(imagecreatetruecolor(300, 300), $file); // create and write image/png to it
        $image = new UploadedFile($file, 'new_image.png');

        $params = ['title' => 'Note with image'];

        $this->client->request(
            'POST', '/api/notes/create', $params, ['image' => $image], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $this->client->request(
            'GET', '/api/notes/5', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $note = json_decode($response->getContent(), true)['note'];

        $this->assertEquals($params['title'], $note['title']);
        $this->assertNotNull($note['image']);
        $this->assertNotNull($note['image_thumbnail']);
    }

    /** @test */
    public function user_can_update_note()
    {
        $token = $this->createUser();

        $params = [
            'title' => 'Updated title',
            'body' => 'Updated body',
        ];

        $this->client->request(
            'POST', '/api/notes/5/update', $params, [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $this->client->request(
            'GET', '/api/notes/5', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $note = json_decode($response->getContent(), true)['note'];

        $this->assertEquals($params['title'], $note['title']);
        $this->assertEquals($params['body'], $note['body']);
    }

    /** @test */
    public function user_can_delete_note()
    {
        $token = $this->createUser();

        $this->client->request(
            'DELETE', '/api/notes/5/delete', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->client->request(
            'GET', '/api/notes/5', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '. $token]
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    private function createUser()
    {
        return $this->client->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => 'test@test.com',
                'password' => '123456'
            ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

}