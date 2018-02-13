<?php

namespace Rumbleship\Test;

use PHPUnit\Framework\TestCase;
use Rumbleship\Gateway;
use Rumbleship\Test\Debug;
use Rumbleship\Test\RequestToBodyMockTransport;

class GatewayTest extends TestCase {
    const HOST = 'api.staging-rumbleship.com';

    public function setUp() {
        $claims = [
            'u'=> 'userhashid',
            'b' => 'buyerhashid',
            's' => 'supplierhashid'
        ];
        $this->jwt = $this->claimsToJwt($claims);
        $this->claims = $claims;
    }

    private function claimsToJwt($claims) {
        $claims_string = base64_encode(json_encode($claims));
        $jwt = "bla.$claims_string.validatehashjwtpart";
        return $jwt;
    }

    /**
     * @group gateway
     * Test that the ready() method returns ready if there is a jwt,
     * and authorized supplier and buyer
     */
    public function testReady() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $this->assertFalse($gateway->ready());
        $insufficient_claims = ['u'=> 'asdf'];
        $gateway->setJwt($this->claimsToJwt($insufficient_claims));
        $this->assertFalse($gateway->ready());
        $gateway->setJwt($this->jwt);
        $this->assertTrue($gateway->ready());
    }

    /**
     * @group gateway
     * request has authorization,
     * is GET
     * is to the correct url
     */
    public function testGetTermsChoices() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $resp = $gateway->getTermsChoices();
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $s = $this->claims['s'];
        $url_expected = "https://" . self::HOST . "/v1/buyers/$b/suppliers/$s/terms-choices";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'GET');
    }

    /**
     * @group gateway
     * request has authorization,
     * is POST
     * is to the correct url
     * posts the data in the request body
     */
    public function testCreatePurchaseOrder() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $data = [ 'key' => 'createPoData' ];
        $resp = $gateway->createPurchaseOrder($data);
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $s = $this->claims['s'];
        $url_expected = "https://" . self::HOST . "/v1/buyers/$b/suppliers/$s/purchase-orders";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'POST');
        $this->assertEquals($resp->body['request_payload'], $data);
    }

    /**
     * @group gateway
     * request has authorization,
     * is POST
     * is to the correct url
     * uses the passed in hashid
     * posts the data in the request body
     */
    public function testConfirmPurchaseOrder() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $data = ['key' => 'confirmPoData'];
        $hashid = 'po_test';
        $resp = $gateway->confirmPurchaseOrder($hashid, $data);
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $s = $this->claims['s'];
        $url_expected = "https://" . self::HOST . "/v1/purchase-orders/$hashid/confirm";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'POST');
        $this->assertEquals($resp->body['request_payload'], $data);
    }

    /**
     * @group gateway
     * request has authorization,
     * is POST
     * is to the correct url
     * uses the passed in hashid
     * posts the data in the request body
     */
    public function testConfirmForShipment() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $data = ['key' => 'confirmPoData'];
        $hashid = 'po_test';
        $resp = $gateway->confirmForShipment($hashid, $data);
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $s = $this->claims['s'];
        $url_expected = "https://" . self::HOST . "/v1/purchase-orders/$hashid/confirm-for-shipment";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'POST');
        $this->assertEquals($resp->body['request_payload'], $data);
    }

    /**
     * @group gateway
     * request has authorization,
     * is POST
     * is to the correct url
     * uses the passed in hashid
     * posts the data in the request body
     */
    public function testCreateShipment() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $data = ['key' => 'createShipment'];
        $hashid = 'po_test';
        $resp = $gateway->createShipment($hashid, $data);
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $s = $this->claims['s'];
        $url_expected = 'https://' . self::HOST . '/v1/purchase-orders/' . $hashid . '/shipments';
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'POST');
        $this->assertEquals($resp->body['request_payload'], $data);
    }

    /**
     * @group gateway
     * request has authorization
     * is GET
     * is to the correct url
     */
    public function testGetBuyerSupplierRelationhip() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $resp = $gateway->getBuyerSupplierRelationship();
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $s = $this->claims['s'];
        $url_expected = "https://" . self::HOST . "/v1/buyers/$b/suppliers/$s";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'GET');
    }

    /**
     * @group gateway
     * request has authorization
     * is GET
     * is to the correct url
     */
    public function testGetBuyerProfile() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $resp = $gateway->getBuyerProfile();
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $b = $this->claims['b'];
        $url_expected = "https://" . self::HOST . "/v1/buyers/$b";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'GET');
    }

    /**
     * @group gateway
     * request has authorization
     * is GET
     * is to the correct url
     */
    public function testGetSupplierProfile() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $gateway->setJwt($this->jwt);
        $resp = $gateway->getSupplierProfile();
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, $this->jwt);
        $s = $this->claims['s'];
        $url_expected = "https://" . self::HOST . "/v1/suppliers/$s";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'GET');
    }


    /**
    * @group current
    */
    public function testGetConfig() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $token = "secrettoken";
        $resp = $gateway->getConfig($token);
        $url_expected = "https://" . self::HOST . "/v1/config?id_token=$token";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'GET');
    }

    /**
     * Gateway Login posts to correct endpoint
     */
    public function testGatewayLogin() {
        $transport = new RequestToBodyMockTransport();
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $credentials = [
            'id_token' => 'mylongidtokenasdfasdfasdf',
            'email' => 'test@rumbleship.com',
            'context' => 'test-gateway',
        ];
        $context = 'test-gateway';
        $resp = $gateway->login($credentials);
        $url_expected = "https://" . self::HOST . "/v1/gateway/login";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'POST');
        $this->assertEquals($resp->body['request_payload']['id_token'], $credentials['id_token']);
        $this->assertEquals($resp->body['request_payload']['email'], $credentials['email']);
    }

    /**
     * Gateway Login with credentials should set the $jwt
     */
    public function testGatewayLoginSetsJWT() {
        // setup our mock response
        $transport = new MockTransport();
        $jwt = 'mock.jsonwebtoken.aasdf';
        $transport->raw_headers =  'authorization: ' . $jwt ."\r\n";
        $transport->code = 201;
        $gateway = new Gateway(self::HOST, ['transport' => $transport]);
        $data = ['id_token' => 'api123key', 'email' => 'test@rumbleship.co'];
        // test the request
        $resp = $gateway->login($data);
        $this->assertEquals($resp->status_code, 201);
        $this->assertEquals($gateway->getJwt(), $jwt);
    }
}
