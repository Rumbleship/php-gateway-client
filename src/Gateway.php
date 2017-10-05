<?php
namespace Rumbleship;

use Rumbleship\Api;


class Gateway extends Api {
    protected $name;

    public function __construct ($request_options = array())
    {
        $this->name = 'Rumbleship Gateway';
        $this->description = 'Endpoint SDK for using the gateway';
    }
}

