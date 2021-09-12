<?php
use PHPUnit\Framework\TestCase;

class VerifyTest extends TestCase
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

    public function testVerifyWithToken()
    {
        $request = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'verify', [
            'headers' => [
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAbGVsZXJlc3RhcGkucnUifQ.UaM9c3jZAOsclP2dQz0uCp22ovAj7Rn4MXG6RO9p4mY'
            ]
        ]);
        $response = $this->http->send($request);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $data = json_decode($response->getBody(), true);
        $this->assertEquals("application/json", $contentType);
        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['result']);
    }

    public function testVerifyWithWrongToken()
    {
        $response = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'verify', [
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

    public function testVerifyWithoutToken()
    {
        $response = $this->http->createRequest('GET', _SHOP_URL_.'module/lelerestapi/'.'verify', $this->params);
        $response = $this->http->send($response);

        $this->assertEquals(403, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $data = json_decode($response->getBody(), true);
        $this->assertEquals("application/json", $contentType);
        $this->assertFalse($data['success']);
        $this->assertNotEmpty($data['errors']);
    }
}