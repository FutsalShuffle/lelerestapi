<?php
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
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

    public function testCartSummaryWithToken()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'cart', [
            'headers' => [
                'Authorization' => 'Bearer '._JWT_TOKEN_
            ]
        ]);
        $response = $this->http->send($request);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $data = json_decode($response->getBody(), true);
        $this->assertEquals("application/json", $contentType);
        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['result']);
        $this->assertNotEmpty($data['result']['cart']);
    }

    public function testCartSummaryWithWrongToken()
    {
        $response = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'cart', [
            'headers' => [
                'Authorization' => 'Bearer eyJ0sdadas'
            ]
        ]);
        $response = $this->http->send($response);

        $this->assertEquals(400, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $data = json_decode($response->getBody(), true);
        $this->assertEquals("application/json", $contentType);
        $this->assertFalse($data['success']);
        $this->assertEmpty($data['result']);
    }

    public function testCartSummaryWithoutToken()
    {
        $response = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'cart', $this->params);
        $response = $this->http->send($response);

        $this->assertEquals(403, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $data = json_decode($response->getBody(), true);
        $this->assertEquals("application/json", $contentType);
        $this->assertFalse($data['success']);
        $this->assertNotEmpty($data['errors']);
    }

    public function testCartAddToCart()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'cart', [
            'headers' => [
                'Authorization' => 'Bearer '._JWT_TOKEN_
            ]
        ]);
        $response = $this->http->send($request);
        $data = json_decode($response->getBody(), true);
        $prevCartCount = count($data['result']['cart']['products']);

        $response = $this->http->createRequest('POST', _SHOP_URL_.'module/lelerestapi/'.'cart', [
            'headers' => [
                'Authorization' => 'Bearer '._JWT_TOKEN_
            ],
            'body' => [
                'id_product' => 6,
                'add' => 1
            ]
        ]);
        $response = $this->http->send($response);
        $data = json_decode($response->getBody(), true);
        $nextCartCount = count($data['result']['cart']['products']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($nextCartCount, $prevCartCount+1);
        $this->assertTrue($data['success']);
    }
}