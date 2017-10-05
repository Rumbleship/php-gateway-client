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
    protected $protocol = 'https://';
    protected $pathPrefix = '';

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

        /* hook up json decoding of body */
        $hooks = new \Requests_Hooks();
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
                $jwt_claims = json_decode(base64_decode($raw_claims), true);
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

        $resp =  $this->post('/login', $credentials);
        $jwt = $resp->headers['authorization'];
        $this->setJwt($jwt);
        return $resp;
    }

    protected function buildUrl($path)
    {
        $p = $this->pathPrefix ? rtrim($this->pathPrefix, '/') . '/' . rtrim($path, '/') : rtrim($path, '/');
        return $this->protocol . $this->host . '/' . $p;
    }

    public function get($path)
    {
        return Requests::get($this->buildUrl($path), $this->headers, $this->requestOptions);
    }

    public function post($path, $data)
    {
        return Requests::post($this->buildUrl($path), $this->headers, $data, $this->requestOptions);
    }

    public function put($path, $data)
    {
        return Requests::put($this->buildUrl($path), $this->headers, $data, $this->requestOptions);
    }

    public function patch($path, $data)
    {
        return Requests::patch($this->buildUrl($path), $this->headers, $data, $this->requestOptions);
    }

    public function delete($path)
    {
        return Requests::delete($this->buildUrl($path), $this->headers, $this->requestOptions);
    }

}


