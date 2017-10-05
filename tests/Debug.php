<?php
namespace Rumbleship\Test;

Class Debug {
    public static function output($data) {
        fwrite(STDERR, print_r($data, TRUE));
    }
}

