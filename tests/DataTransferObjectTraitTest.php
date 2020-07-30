<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017-2020 Artem Rodygin
//
//  You should have received a copy of the MIT License along with
//  this file. If not, see <http://opensource.org/licenses/MIT>.
//
//----------------------------------------------------------------------

namespace Tests\Webinarium;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/DTO.php';

/**
 * @coversDefaultClass \Webinarium\DataTransferObjectTrait
 */
class DataTransferObjectTraitTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testDefault()
    {
        $command = new DTO();

        self::assertSame(1, $command->property);
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization()
    {
        $command = new DTO(['property' => 2]);

        self::assertSame(2, $command->property);
    }
}
