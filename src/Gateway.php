<?php

namespace Rumbleship;

class Gateway {
  protected $name;
  public function __construct ($options = array()) {
    $this->name = 'Rumbleship Gateway';

    if (!isset($options['apihost']))
      $options["apihost"] = 'api.staging-rumbleship.com';
  }

  public function words() {
    return 'Hello ' . $this->name . '.';
  }

}

