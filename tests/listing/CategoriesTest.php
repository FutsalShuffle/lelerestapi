<?php
use PHPUnit\Framework\TestCase;

class CategoriesTest extends TestCase
{
    private $http;
    private $params;

    public function setUp() : void
    {
        $this->http = new GuzzleHttp\Client(['defaults' => [ 'exceptions' => false ]]);
        $this->params = ['exceptions' => false];
    }

    public function tearDown() : void
    {
        $this->http = null;
    }

    public function testCategoriesResponseCode()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'categories', $this->params);
        $response = $this->http->send($request);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $data = json_decode($response->getBody(), true);
        $this->assertEquals("application/json", $contentType);
        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['result']['categories']);
    }
}