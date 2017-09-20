<?php
namespace Rumbleship;

use Requests;
use Rumbleship\Test\Debug;

class Api {
  protected $name;
  protected $description;
  protected $request_options;
  protected $headers;
  protected $credentials;
  protected $jwt;
  protected $protocol;
  protected $host;
  protected $path_prefix;

  public function __construct ($config = array())
  {
    $this->name = 'Api';
    $this->description = 'Facilitating connection to the Rumbleship API';
    $this->headers = array();
    $this->headers['Accept'] =  'application/json';

    if (!isset($config['host']))
      $config['host'] = 'api.staging-rumbleship.com';
    if (!isset($config['jwt']))
      $config['jwt'] = '';
    if (!isset($config['credentials']))
      $config['credentials'] = array();
    if (!isset($config['request_options']))
      $config['request_options'] = array();
    if (!isset($config['https']))
      $config['useHttps'] = true;
    if (!isset($config['path_prefix']))
      $config['useHttps'] = true;

    $this->host = rtrim($config['host'], "/") ;
    $this->host = rtrim($config['host'], "/") ;
    $this->credentials = $config['credentials'];
    $this->request_options = $config['request_options'];
    $this->setJwt($config['jwt']);
    $this->protocol = $config['useHttps'] ? 'https://' : 'http://';

    $hooks = new \Requests_Hooks();
    $hooks->register('requests.after_request', function ($resp) { $resp->body = json_decode($resp->body, true);});
    $this->request_options['hooks'] = $hooks;
  }

  protected function setJwt($jwt = '') {
    if ($jwt)
      $this->headers['Authorization'] = $jwt;
    $this->jwt = $jwt;
  }
  protected function buildUrl($path)
  {
    $p = $this->path_prefix ? rtrim($this->path_prefix, '/') . '/' . rtrim($path, '/') : rtrim($path, '/');
    return $this->protocol . $this->host . '/' . $p;
  }

  public function getJwt() {
    return $this->jwt;
  }

  public function get($path)
  {
    return Requests::get($this->buildUrl($path), $this->headers, $this->request_options);
  }

  public function post($path, $data)
  {
    return Requests::post($this->buildUrl($path), $this->headers, $data, $this->request_options);
  }

  public function put($path, $data)
  {
    return Requests::put($this->buildUrl($path), $this->headers, $data, $this->request_options);
  }

  public function patch($path, $data)
  {
    return Requests::patch($this->buildUrl($path), $this->headers, $data, $this->request_options);
  }

}


