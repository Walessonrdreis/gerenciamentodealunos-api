<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ApiRoutesTest extends TestCase
{
    private $client;
    private $baseUri = 'https://seoeads.com/api';

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'http_errors' => false, // Não lançar exceções para respostas de erro HTTP
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function testLoginRoute()
    {
        $response = $this->client->post('/auth/login', [
            'json' => [
                'username' => 'testuser',
                'password' => 'testpassword'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('token', $responseData);
    }

    public function testVerifyTokenRoute()
    {
        // Primeiro, fazer login para obter um token
        $loginResponse = $this->client->post('/auth/login', [
            'json' => [
                'username' => 'testuser',
                'password' => 'testpassword'
            ]
        ]);

        $loginData = json_decode($loginResponse->getBody()->getContents(), true);
        $token = $loginData['token'];

        // Agora, testar a rota de verificação do token
        $response = $this->client->get('/auth/verify', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('success', $responseData);
    }

    public function testInvalidLoginCredentials()
    {
        $response = $this->client->post('/auth/login', [
            'json' => [
                'username' => 'invalid',
                'password' => 'invalid'
            ]
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $responseData);
    }
} 