<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ApiRoutesTest extends TestCase
{
    private $client;
    private $baseUri = 'https://seoeads.com';
    private $adminCredentials = [
        'email' => 'admin@escola.com',
        'password' => '123456'
    ];

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    private function extractJsonFromResponse(string $response): ?array
    {
        // Encontrar o início do JSON (primeiro '{')
        $jsonStart = strpos($response, '{');
        if ($jsonStart === false) {
            return null;
        }

        // Extrair apenas a parte JSON da resposta
        $jsonString = substr($response, $jsonStart);
        return json_decode($jsonString, true);
    }

    public function testAdminLoginRoute()
    {
        $response = $this->client->post('/api/auth/login', [
            'json' => $this->adminCredentials
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        echo "\nAdmin Login Response Status: " . $statusCode;
        echo "\nAdmin Login Response Body: " . $body . "\n";

        $this->assertEquals(200, $statusCode);
        
        $responseData = $this->extractJsonFromResponse($body);
        $this->assertNotNull($responseData, "Could not extract JSON from response: " . $body);
        $this->assertArrayHasKey('token', $responseData);
        
        // Verificar se há informações adicionais do usuário na resposta
        if (isset($responseData['user'])) {
            $this->assertEquals('admin@escola.com', $responseData['user']['email']);
            $this->assertEquals('admin', $responseData['user']['role']);
            $this->assertEquals('active', $responseData['user']['status']);
        }
    }

    public function testAdminVerifyTokenRoute()
    {
        // Primeiro, fazer login como admin
        $loginResponse = $this->client->post('/api/auth/login', [
            'json' => $this->adminCredentials
        ]);

        $loginBody = $loginResponse->getBody()->getContents();
        echo "\nAdmin Login Response: " . $loginBody . "\n";
        
        $loginData = $this->extractJsonFromResponse($loginBody);
        $this->assertNotNull($loginData, "Could not extract JSON from login response: " . $loginBody);
        $this->assertArrayHasKey('token', $loginData, "Token not found in response");
        
        $token = $loginData['token'];

        // Verificar o token
        $response = $this->client->get('/api/auth/verify', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        echo "\nVerify Response Status: " . $statusCode;
        echo "\nVerify Response Body: " . $body . "\n";

        $this->assertEquals(200, $statusCode);
        
        $responseData = $this->extractJsonFromResponse($body);
        $this->assertNotNull($responseData, "Could not extract JSON from response: " . $body);
        $this->assertArrayHasKey('success', $responseData);
        
        // Verificar informações do usuário no payload do token
        if (isset($responseData['user'])) {
            $userData = $responseData['user'];
            $this->assertArrayHasKey('data', $userData);
            $this->assertEquals('admin@escola.com', $userData['data']['email'] ?? null);
            $this->assertEquals('admin', $userData['data']['role'] ?? null);
        }
    }

    public function testInvalidLoginCredentials()
    {
        $response = $this->client->post('/api/auth/login', [
            'json' => [
                'email' => 'invalid@email.com',
                'password' => 'wrongpassword'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        echo "\nInvalid Login Response Status: " . $statusCode;
        echo "\nInvalid Login Response Body: " . $body . "\n";

        $this->assertEquals(401, $statusCode);
        
        $responseData = $this->extractJsonFromResponse($body);
        $this->assertNotNull($responseData, "Could not extract JSON from response: " . $body);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function testInvalidToken()
    {
        $response = $this->client->get('/api/auth/verify', [
            'headers' => [
                'Authorization' => 'Bearer invalidtoken123'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        echo "\nInvalid Token Response Status: " . $statusCode;
        echo "\nInvalid Token Response Body: " . $body . "\n";

        $this->assertEquals(401, $statusCode);
        
        $responseData = $this->extractJsonFromResponse($body);
        $this->assertNotNull($responseData, "Could not extract JSON from response: " . $body);
        $this->assertArrayHasKey('error', $responseData);
    }
}