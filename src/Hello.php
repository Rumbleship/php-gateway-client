<?php

namespace Rumbleship;

class Hello {
    public function __construct($name) {
        $this->name = $name;
    }

    public function words() {
        return 'Hello ' . $this->name . '.';
    }

}
