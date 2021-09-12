<?php
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
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

    public function testCategoryInvalidRequest()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'category', $this->params);
        $response = $this->http->send($request);

        $this->assertEquals(400, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    public function testCategoryResponseCode()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'category?id_category=2', $this->params);
        $response = $this->http->send($request);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    public function testCategoryData()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'category?id_category=2', $this->params);
        $response = $this->http->send($request);

        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['result']['category']);
    }

    public function testCategoryInvalidData()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'category?id_category=9999999', $this->params);
        $response = $this->http->send($request);

        $data = json_decode($response->getBody(), true);
        $this->assertFalse($data['success']);
        $this->assertEmpty($data['result']);
        $this->assertNotEmpty($data['errors']);
        $this->assertEquals(404, $response->getStatusCode());
    }
}