<?php
namespace Rumbleship\Test;

class Debug {
    public static function output($data, $stdout = STDERR) {
        fwrite(STDERR, print_r($data, true));
    }
}
