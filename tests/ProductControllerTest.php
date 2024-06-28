<?php

namespace App\Tests;

use App\Tests\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    use Factories;

    /**
     * @param string $endpoint
     * @param string $method
     * @return void
     * @dataProvider apiEndpoint
     */
    public function testCanNotAccessEndpointsWithoutAuthToken(string $endpoint, string $method): void
    {
        $client = static::createClient();
        $client->request($method, $endpoint);

        $actualResponse = json_decode(
            $client->getResponse()->getContent(),
            true
        );

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('code', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);

        $this->assertEquals(401, $actualResponse['code']);
        $this->assertEquals('JWT Token not found', $actualResponse['message']);
    }

    public function testAddProduct(): void
    {
        $client = $this->createAuthenticatedClient();
        $input = [
            'name' => 'Test',
            'price' => 4000,
            'description' => 'This is a test',
        ];

        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($input)
        );

        $actualResponse = json_decode(
            $client->getResponse()->getContent(),
            true
        );

        $this->assertArrayHasKey('data', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);
        $this->assertEquals('Product created successfully', $actualResponse['message']);

        $this->assertArrayHasKey('id', $actualResponse['data']);
        $this->assertEquals($input['name'], $actualResponse['data']['name']);
        $this->assertEquals($input['price'], $actualResponse['data']['price']);
        $this->assertEquals($input['description'], $actualResponse['data']['description']);
    }

    /**
     * @param string $key
     * @param array<string, mixed> $input
     * @param string $validationMessage
     * @return void
     * @dataProvider getFieldAndValidationMessage
     */
    public function testCannotAddProductWithNameOrPrice(string $key, array $input, string $validationMessage): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($input)
        );

        $this->assertResponseStatusCodeSame(400);
        $actualResponse = json_decode(
            $client->getResponse()->getContent(),
            true
        );

        $this->assertArrayHasKey('errors', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);
        $this->assertEquals('Validation failed', $actualResponse['message']);

        foreach ($actualResponse['errors'] as $error) {
            $this->assertEquals($key, $error['property']);
            $this->assertEquals($validationMessage, $error['message']);
        }
    }

    public function testFetchProducts(): void
    {
        $client = $this->createAuthenticatedClient();

        ProductFactory::createMany(5);

        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $actualResponse =  json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);
        $this->assertEquals('Fetched products successfully', $actualResponse['message']);
        $this->assertNotEmpty($actualResponse['data']);

        foreach ($actualResponse['data'] as $product) {
            $this->assertArrayHasKey('id', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('description', $product);
        }
    }

    public function testFetchAProduct(): void
    {
        $client = $this->createAuthenticatedClient();

        $product = ProductFactory::createMany(2);
        $client->request('GET', "/api/products/{$product[0]->getId()}");

        $this->assertResponseIsSuccessful();
        $actualResponse =  json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);
        $this->assertEquals('Fetched product successfully', $actualResponse['message']);
        $this->assertNotEmpty($actualResponse['data']);

        $this->assertArrayHasKey('id', $actualResponse['data']);
        $this->assertArrayHasKey('name', $actualResponse['data']);
        $this->assertArrayHasKey('price', $actualResponse['data']);
        $this->assertArrayHasKey('description', $actualResponse['data']);
    }

    public function testUpdateAProduct(): void
    {
        $client = $this->createAuthenticatedClient();

        $input = [
            'name' => 'Test',
            'price' => 4000,
            'description' => 'This is a test',
        ];

        $product = ProductFactory::createMany(2);
        $oldValue = [
            'id' => $product[0]->getId(),
            'name' => $product[0]->getName(),
            'price' => $product[0]->getPrice(),
            'description' => $product[0]->getDescription()
        ];

        $client->request(
            'PUT',
            "/api/products/{$product[0]->getId()}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($input)
        );

        $this->assertResponseIsSuccessful();
        $actualResponse = json_decode($client->getResponse()->getContent(), true);;

        $this->assertArrayHasKey('data', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);
        $this->assertEquals('Product updated successfully', $actualResponse['message']);
        $this->assertNotEmpty($actualResponse['data']);

        $this->assertEquals($oldValue['id'], $actualResponse['data']['id']);

        //assert values are no longer as before
        $this->assertNotEquals($oldValue['name'], $actualResponse['data']['name']);
        $this->assertNotEquals($oldValue['price'], $actualResponse['data']['price']);
        $this->assertNotEquals($oldValue['description'], $actualResponse['data']['description']);

        //assert new values have been set
        $this->assertEquals($input['name'], $actualResponse['data']['name']);
        $this->assertEquals($input['price'], $actualResponse['data']['price']);
        $this->assertEquals($input['description'], $actualResponse['data']['description']);
    }

    public function testDeleteAProduct(): void
    {
        $client = $this->createAuthenticatedClient();

        $product = ProductFactory::createMany(2);
        $id = $product[0]->getId();

        $client->request(
            'DELETE',
            "/api/products/{$id}"
        );

        $this->assertResponseIsSuccessful();
        $actualResponse = json_decode($client->getResponse()->getContent(), true);;

        $this->assertArrayNotHasKey('data', $actualResponse);
        $this->assertArrayHasKey('message', $actualResponse);
        $this->assertEquals('Product deleted successfully', $actualResponse['message']);

        //re-fetch product to ensure it doesn't exist
        $client->request(
            'GET',
            "/api/products/{$id}"
        );

        $actualResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Route/Entity not found', $actualResponse['message']);
        $this->assertEquals(404, $actualResponse['status']);
    }

    /** @return array<array{key: string, input: array<string, mixed>, validationMessage: string}> */
    public function getFieldAndValidationMessage(): array
    {
        return [
            [
                'key' => 'name',
                'input' => [
                    'price' => 4000,
                    'description' => 'This is a test',
                ],
                'validationMessage' => 'Name field is required'
            ],
            [
                'key' => 'price',
                'input' => [
                    'name' => 'Test',
                    'description' => 'This is a test',
                ],
                'validationMessage' => 'Price field is required'
            ],
            [
                'key' => 'price',
                'input' => [
                    'name' => 'This is a test',
                    'price' => null,
                    'description' => 'This is a test',
                ],
                'validationMessage' => 'Price field is required'
            ],
            [
                'key' => 'name',
                'input' => [
                    'price' => 4000,
                    'name' => null,
                    'description' => 'This is a test',
                ],
                'validationMessage' => 'Name field is required'
            ],
        ];
    }

    /** @return array<array{string, string}> */
    public function apiEndpoint(): array
    {
        return [
            ['api/products', 'GET'],
            ['api/products', 'POST'],
            ['api/products/1', 'GET'],
            ['api/products/1', 'PUT'],
            ['api/products/1', 'DELETE'],
        ];
    }

    /**
     *
     * @return KernelBrowser
     */
    private function createAuthenticatedClient(): KernelBrowser
    {
        $username = 'hagioswilson@gmail.com';
        $password = 'password';

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
