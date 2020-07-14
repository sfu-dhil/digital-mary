<?php

namespace App\Tests\Util;

use App\Util\Initializer;
use PHPUnit\Framework\TestCase;

class InitializerTest extends TestCase
{

    public function getData() {
        return [
            ['MM', 'Mickey Mouse'],
            ['MI', 'Mickey'],
            ['MO', 'Mickey O\'Mouse'],
            ['MM', 'Mickey McDonald'],
            ['MM', 'Mickey Donald Mouse'],
            ['ZM', 'Zoë Mouse'],
            ['SF', 'Sørina François'],
            ['', ''],
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testGenerate($expected, $data)
    {
        $this->assertEquals($expected, Initializer::generate($data));
        $this->assertTrue(true);
    }
}
