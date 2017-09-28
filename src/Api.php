<?php
namespace Rumbleship;

use Requests;
use Rumbleship\Test\Debug;

class Api {
  protected $name;
  protected $description;
  protected $host;
  protected $request_options;

  protected $headers = array();
  protected $jwt = '';
  protected $protocol = 'https://';
  protected $path_prefix = '';

  public function __construct ($host, $request_options = array())
  {
    $this->name = 'Api';
    $this->description = 'Facilitating connection to the Rumbleship API';
    $this->host = rtrim($host, "/") ;
    $this->request_options = $request_options;
    $this->headers['Accept'] =  'application/json';

    /* hook up json decoding of body */
    $hooks = new \Requests_Hooks();
    $hooks->register('requests.after_request', function ($resp) { $resp->body = json_decode($resp->body, true);});
    $this->request_options['hooks'] = $hooks;
  }

  public function setJwt($jwt = '')
  {
    if ($jwt)
      $this->headers['Authorization'] = $jwt;
    $this->jwt = $jwt;
  }

  public function getJwt()
  {
    return $this->jwt;
  }

  public function login($credentials)
  {
    if (!is_array($credentials))
      throw new Exception('Login requires first param to be an Associative Array');

    $resp =  $this->post('/login', $credentials);
    $jwt = $resp->headers['authorization'];
    $this->setJwt($jwt);
    return $resp;
  }

  protected function buildUrl($path)
  {
    $p = $this->path_prefix ? rtrim($this->path_prefix, '/') . '/' . rtrim($path, '/') : rtrim($path, '/');
    return $this->protocol . $this->host . '/' . $p;
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

  public function delete($path)
  {
    return Requests::delete($this->buildUrl($path), $this->headers, $this->request_options);
  }

}


