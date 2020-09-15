<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Util;

use App\Util\Initializer;
use PHPUnit\Framework\TestCase;

class InitializerTest extends TestCase {
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
     *
     * @param mixed $expected
     * @param mixed $data
     */
    public function testGenerate($expected, $data) : void {
        $this->assertSame($expected, Initializer::generate($data));
        $this->assertTrue(true);
    }
}
