<?php
namespace Rumbleship\Test;

use Rumbleship\Test\MockTransport;

class RequestToBodyMockTransport extends MockTransport {
    public function request($url, $headers = [], $data = [], $options = []) {
        $body_data = [
      'headers'=> $headers,
      'url'=> $url,
      'request_payload'=> $data,
      'options'=> $options
    ];
        $this->body = json_encode($body_data);
        return parent::request($url, $headers = [], $data = [], $options = []);
    }
}
