<?php
use PHPUnit\Framework\TestCase;
use Rumbleship\Hello;

class HelloTest extends TestCase {
    public function testHello() {
        $hello = new Hello('Alex');
        $this->assertEquals('Hello Alex.', $hello->words());
    }
}
