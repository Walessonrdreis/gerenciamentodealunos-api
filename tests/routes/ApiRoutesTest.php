<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ApiRoutesTest extends TestCase
{
    private $client;
    private $baseUri = 'https://seoeads.com';

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

    public function testLoginRoute()
    {
        $response = $this->client->post('/api/auth/login', [
            'json' => [
                'username' => 'testuser',
                'password' => 'testpassword'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        echo "\nResponse Status: " . $statusCode;
        echo "\nResponse Body: " . $body . "\n";

        $this->assertEquals(200, $statusCode);
        
        $responseData = $this->extractJsonFromResponse($body);
        $this->assertNotNull($responseData, "Could not extract JSON from response: " . $body);
        $this->assertArrayHasKey('token', $responseData);
    }

    public function testVerifyTokenRoute()
    {
        // Primeiro, fazer login para obter um token
        $loginResponse = $this->client->post('/api/auth/login', [
            'json' => [
                'username' => 'testuser',
                'password' => 'testpassword'
            ]
        ]);

        $loginBody = $loginResponse->getBody()->getContents();
        echo "\nLogin Response: " . $loginBody . "\n";
        
        $loginData = $this->extractJsonFromResponse($loginBody);
        $this->assertNotNull($loginData, "Could not extract JSON from login response: " . $loginBody);
        $this->assertArrayHasKey('token', $loginData, "Token not found in response");
        
        $token = $loginData['token'];

        // Agora, testar a rota de verificação do token
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
    }

    public function testInvalidLoginCredentials()
    {
        $response = $this->client->post('/api/auth/login', [
            'json' => [
                'username' => 'invalid',
                'password' => 'invalid'
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
}