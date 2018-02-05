<?php
namespace Rumbleship;

use Requests;
use Rumbleship\Test\Debug;

class Api {
    protected $name;
    protected $description;
    protected $host;
    protected $requestOptions;

    protected $headers = array();
    protected $jwt = '';
    protected $authorizedBuyer = '';
    protected $authorizedSupplier = '';
    protected $authorizedClaims = '';
    protected $pathPrefix = '';
    public $protocol = 'https://';

    /**
     * @param {string} $host
     * @param {array} $request_options
     */
    public function __construct ($host, $request_options = array())
    {
        $this->name = 'Api';
        $this->description = 'Facilitating connection to the Rumbleship API';
        $this->host = rtrim($host, "/") ;
        $this->requestOptions = $request_options;
        $this->headers['Accept'] =  'application/json';


        $hooks = new \Requests_Hooks();

        /* Encode nested array-like payload values as JSON */
        $hooks->register( 'requests.before_request', function ( &$url, &$headers, &$data, &$type, &$options ) {
          if ( is_array( $data ) || is_object( $data ) ) {
            foreach ( $data as $key => $val ) {
              if ( is_array( $val ) || is_object( $val ) ) {
                $data[$key] = json_encode( $val );
              }
            }
          }
        });

        /* hook up json decoding of body */
        $hooks->register('requests.after_request', function ($resp) { $resp->body = json_decode($resp->body, true);});
        $this->requestOptions['hooks'] = $hooks;
    }

    public function unsetJwt()
    {
        $this->setJwt('');

    }
    public function setJwt($jwt)
    {
        if ($jwt) {
            $this->jwt = $jwt;
            $this->headers['Authorization'] = $jwt;
            $jwt_exploded = explode('.', $jwt);
            if (isset($jwt_exploded[1])){
                $raw_claims = explode('.', $jwt)[1];
                $jwt_claims = json_decode(base64_decode(strtr($raw_claims, '-_', '+/')), true);
                $this->authorizedClaims = $jwt_claims;
                if(isset($jwt_claims['b'])){
                    $this->authorizedBuyer = $jwt_claims['b'];
                }
                if(isset($jwt_claims['s'])){
                    $this->authorizedSupplier = $jwt_claims['s'];
                }
            } else {
                error_log("Invalid JWT $jwt \Rumbleship\Api#setJwt() ");
            }
        } else {
            $this->jwt = '';
            $this->authorizedClaims ='';
            $this->authorizedBuyer = '';
            $this->authorizedSupplier = '';
        }
    }

    public function getAuthorizedClaims()
    {
        return $this->authorizedClaims;
    }

    public function getAuthorizedSupplier()
    {
        return $this->authorizedSupplier;
    }

    public function getAuthorizedBuyer()
    {
        return $this->authorizedBuyer;
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function login($credentials)
    {
        if (!is_array($credentials))
            throw new Exception('Login requires first param to be an Associative Array');

        return $this->post('v1/login', $credentials);
    }

    protected function buildUrl($path)
    {
        $p = $this->pathPrefix ? rtrim($this->pathPrefix, '/') . '/' . rtrim($path, '/') : rtrim($path, '/');
        return $this->protocol . $this->host . '/' . $p;
    }

    public function get($path)
    {
        return $this->handleRequest('get', $path);
    }

    public function post($path, $data)
    {
        return $this->handleRequest('post', $path, $data);
    }

    public function put($path, $data)
    {
        return $this->handleRequest('put', $path, $data);
    }

    public function patch($path, $data)
    {
        return $this->handleRequest('patch', $path, $data);
    }

    public function delete($path)
    {
        return $this->handleRequest('delete', $path);
    }

    protected function handleRequest($method, $path, $data=[])
    {
        $resp;
        switch($method) {
            case 'get':
                $resp = Requests::get($this->buildUrl($path), $this->headers, $this->requestOptions);
                break;
            case 'post':
                $resp = Requests::post($this->buildUrl($path), $this->headers, $data, $this->requestOptions);
                break;
            case 'put':
                $resp = Requests::put($this->buildUrl($path), $this->headers, $data, $this->requestOptions);
                break;
            case 'patch':
                $resp = Requests::patch($this->buildUrl($path), $this->headers, $data, $this->requestOptions);
                break;
            case 'delete':
                $resp = Requests::delete($this->buildUrl($path), $this->headers, $this->requestOptions);
                break;
        }
        $jwt = $resp->headers['authorization'];
        $this->setJwt($jwt);
        return $resp;
    }

}
