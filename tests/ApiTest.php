<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Rumbleship\Api;
use Rumbleship\Test\Debug;
/* use Test\MockTransport; */

class ApiTest extends TestCase {
  const TEST_ID_TOKEN= 'api123key';
  const TEST_EMAIL= 'lockwood+test@rumbleship.com';
  const HOST = 'api.staging-rumbleship.com';

  function setUp() {
    $this->defaultConfig = array(
      'credentials' => array( "id_token" => self::TEST_ID_TOKEN, "email" => self::TEST_EMAIL ),
      'host' => self::HOST
    );
  }

  function testGetRootOnAlphaStaging() {
    $api = new Api($this->defaultConfig);
    $resp = $api->get('/');
    $this->assertEquals($resp->status_code, 200);
    // decodes json to php array()
    $this->assertEquals($resp->body['name'], 'alpha');
  }
}


