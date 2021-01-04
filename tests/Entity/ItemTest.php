<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase {
    public function testAddDuplicateRevision() : void {
        $item = new Item();
        $item->addRevision('2020-02-02', 'MJ');
        $item->addRevision('2020-02-02', 'MJ');
        $this->assertCount(1, $item->getRevisions());
    }

    public function testAddRevision() : void {
        $item = new Item();
        $item->addRevision('2020-02-02', 'MJ');
        $this->assertCount(1, $item->getRevisions());
    }

    public function testGetRevisions() : void {
        $data = [
            ['date' => '2020-03-07', 'initials' => 'RC'],
            ['date' => '2020-01-01', 'initials' => 'MJ'],
            ['date' => '2020-03-07', 'initials' => 'MJ'],
        ];

        $item = new Item();

        foreach ($data as $d) {
            $item->addRevision($d['date'], $d['initials']);
        }
        $revisions = $item->getRevisions();
        $this->assertSame($data[2], $revisions[0]);
        $this->assertSame($data[0], $revisions[1]);
        $this->assertSame($data[1], $revisions[2]);
    }
}
