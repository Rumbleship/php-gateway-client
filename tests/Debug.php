<?php
namespace Rumbleship\Test;

Class Debug {
    public static function output($data, $stdout = STDERR) {
        fwrite(STDERR, print_r($data, TRUE));
    }
}

