<?php
namespace Rumbleship\Test\Api;

use PHPUnit\Framework\TestCase;
use Rumbleship\Api;
use Rumbleship\Test\Debug;
use Rumbleship\Test\MockTransport;
use Rumbleship\Test\RequestToBodyMockTransport;



class ApiTest extends TestCase {
    const TEST_ID_TOKEN= 'api123key';
    const TEST_EMAIL= 'lockwood+test@rumbleship.com';
    const HOST = 'api.staging-rumbleship.com';
    const JWT = 'my.test.jwt';

    function setUp() {
    }

    /**
     * Encode nested array-like payload values as JSON
     */
    function testNestedJsonEncode() {
      $transport = new RequestToBodyMockTransport();
      $api = new Api( self::HOST, array( 'transport' => $transport ) );
      $data_arr = [
        'arr' => ['str', 1, 2, 3, (object)[4, 5, 'str'], new \stdClass],
        'obj' => (object)['str', 1, 2, 3, (object)[4, 5, 'str'], new \stdClass],
        'obj2' => new \stdClass,
      ];
      $resp = $api->post( '/', $data_arr );
      foreach ( $resp->body['request_payload'] as $key => $val ) {
        $this->assertEquals( gettype( $val ), 'string' );
        $this->assertEquals( json_decode( $val ), $data_arr[$key] );
      }
    }

    /**
     * Use the actual default transport and talk to an external url, our staging site
     */
    function testStagingUrl()
    {
        $api = new Api(self::HOST);
        $resp = $api->get('/');
        $this->assertEquals($resp->status_code, 200);
        // decodes json to php array()
        $this->assertEquals($resp->body['name'], 'alpha');
    }

    /**
     * Able to set the jwt via getJwt(), and retrieve it with setJwt()
     */
    function testGetSetJwt()
    {
        $api = new Api(self::HOST);
        $api->setJwt(self::JWT);
        $this->assertEquals($api->getJwt(), self::JWT);
    }

    /**
     * Login posts to correct endpoint
     */
    function testLogin()
    {
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $credentials = array(
            'id_token' => 'mylongidtokenasdfasdfasdf',
            'email' => 'test@rumbleship.com'
        );
        $resp = $api->login($credentials);
        $url_expected = "https://" . self::HOST . "/v1/login";
        $this->assertEquals($resp->body['url'], $url_expected);
        $this->assertEquals($resp->body['options']['type'], 'POST');
        $this->assertEquals($resp->body['request_payload']['id_token'], $credentials['id_token']);
        $this->assertEquals($resp->body['request_payload']['email'], $credentials['email']);
    }

    /**
     * Login with credentials should set the $jwt
     */
    function testLoginSetsJWT()
    {
        // setup our mock response
        $transport = new MockTransport();
        $jwt = 'mock.jsonwebtoken.aasdf';
        $transport->raw_headers =  'authorization: ' . $jwt ."\r\n";
        $transport->code = 201;
        $api = new Api(self::HOST, array('transport' => $transport));
        $data = array('id_token' => self::TEST_ID_TOKEN, 'email' => self::TEST_EMAIL);
        // test the request
        $resp = $api->login($data);
        $this->assertEquals($resp->status_code, 201);
        $this->assertEquals($api->getJwt(), $jwt);
    }

    /**
     * When a jwt is set, a request will include the authorization
     */
    function testSetJwtUpdatesAuthorizationHeadersForRequests()
    {
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $api->setJwt(self::JWT);
        $resp = $api->get('/');
        $authorized_with_token = $resp->body['headers']['Authorization'];
        $this->assertEquals($authorized_with_token, self::JWT);
    }

    /**
     * When a jwt is set, authorized{Buyer|sUpplier|Claims} properties are set
     */
    function testSetJwtShouldSetAuthorizedPropertiesThenClearJwtShouldClearThem()
    {
        $claims = array(
            'u'=> 'userhashid',
            'b' => 'buyerhashid',
            's' => 'supplierhashid'
        );
        $claims_string = base64_encode(json_encode($claims));
        $claims_jwt = "bla.$claims_string.validatehashjwtpart";
        $api = new Api(self::HOST);
        $api->setJwt($claims_jwt);
        $this->assertEquals($api->getJwt(), $claims_jwt);
        $this->assertEquals($api->getAuthorizedClaims(), $claims);
        $this->assertEquals($api->getAuthorizedBuyer(), $claims['b']);
        $this->assertEquals($api->getAuthorizedSupplier(), $claims['s']);
        $api->unsetJwt();
        $this->assertEquals($api->getJwt(), '');
        $this->assertEquals($api->getAuthorizedClaims(), '');
        $this->assertEquals($api->getAuthorizedBuyer(), '');
        $this->assertEquals($api->getAuthorizedSupplier(), '');
    }

    function testPostIsPost()
    {
        // setup
        $request_data = array('key' => 'value');
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $api->setJwt(self::JWT);
        // call
        $resp = $api->post('/', $request_data );
        $this->assertTrue($resp instanceof \Requests_Response);
        $this->assertEquals($resp->body['request_payload']['key'], $request_data['key']);
        $this->assertEquals($resp->body['options']['type'], 'POST');
    }

    function testPutIsPut()
    {
        // setup
        $request_data = array('key' => 'value');
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $api->setJwt(self::JWT);
        // call
        $resp = $api->put('/', $request_data );
        $this->assertTrue($resp instanceof \Requests_Response);
        $this->assertEquals($resp->body['request_payload']['key'], $request_data['key']);
        $this->assertEquals($resp->body['options']['type'], 'PUT');
    }

    function testPatchIsPatch()
    {
        // setup
        $request_data = array('key' => 'value');
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $api->setJwt(self::JWT);
        // call
        $resp = $api->patch('/', $request_data );
        $this->assertTrue($resp instanceof \Requests_Response);
        $this->assertEquals($resp->body['request_payload']['key'], $request_data['key']);
        $this->assertEquals($resp->body['options']['type'], 'PATCH');
    }

    function testGetIsGet()
    {
        // setup
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $api->setJwt(self::JWT);
        // call
        $resp = $api->get('/');
        $this->assertTrue($resp instanceof \Requests_Response);
        $this->assertEquals($resp->body['options']['type'], 'GET');
    }

    function testDeleteIsDelete()
    {
        // setup
        $transport = new RequestToBodyMockTransport();
        $api = new Api(self::HOST, array('transport' => $transport));
        $api->setJwt(self::JWT);
        // call
        $resp = $api->delete('/');
        $this->assertTrue($resp instanceof \Requests_Response);
        $this->assertEquals($resp->body['options']['type'], 'DELETE');
    }
}
