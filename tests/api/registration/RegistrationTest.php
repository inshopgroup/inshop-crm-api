<?php

namespace App\Tests\api\registration;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Client;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RegistrationTest extends ApiTestCase
{
    private ?Client $client = null;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $client = $em->getRepository(Client::class)->findOneBy(['username' => 'test@test.test']);

        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testRegistration(): void
    {
        static::createClient()->request(
            'POST',
            '/frontend/signup',
            [
                'json' => [
                    'username' => 'test@test.test',
                    'name' => 'Test',
                    'plainPassword' => 'test@test.test',
                ],
            ]
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginFailure(): void
    {
        self::assertInstanceOf(Client::class, $this->client);

        static::createClient()->request(
            'POST',
            '/frontend/login',
            [
                'json' => [
                    'username' => 'test@test.test',
                    'password' => 'test@test.test',
                ],
            ]
        );
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @return string
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testLoginByToken(): string
    {
        self::assertInstanceOf(Client::class, $this->client);

        $response = static::createClient()->request(
            'GET',
            sprintf('/frontend/login/%s', $this->client->getToken()),
        );
        self::assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $data);

        return $data['token'];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginTest(): void
    {
        self::assertInstanceOf(Client::class, $this->client);

        static::createClient()->request(
            'POST',
            '/frontend/login',
            [
                'json' => [
                    'username' => 'test@test.test',
                    'password' => 'test@test.test',
                ],
            ]
        );
        self::assertResponseIsSuccessful();
    }
}
