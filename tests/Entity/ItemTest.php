<?php

namespace App\Tests\Entity;

use App\Entity\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase {
    public function testAddDuplicateRevision() {
        $item = new Item();
        $item->addRevision('2020-02-02', 'MJ');
        $item->addRevision('2020-02-02', 'MJ');
        $this->assertCount(1, $item->getRevisions());
    }

    public function testAddRevision() {
        $item = new Item();
        $item->addRevision('2020-02-02', 'MJ');
        $this->assertCount(1, $item->getRevisions());
    }

    public function testGetRevisions() {
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
        $this->assertEquals($data[2], $revisions[0]);
        $this->assertEquals($data[0], $revisions[1]);
        $this->assertEquals($data[1], $revisions[2]);
    }
}
