<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017 Artem Rodygin
//
//  You should have received a copy of the MIT License along with
//  this file. If not, see <http://opensource.org/licenses/MIT>.
//
//----------------------------------------------------------------------

namespace Tests\Webinarium;

require_once __DIR__ . '/DTO.php';

class DataTransferObjectTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $command = new DTO();

        self::assertSame(1, $command->property);
    }

    public function testInitialization()
    {
        $command = new DTO(['property' => 2]);

        self::assertSame(2, $command->property);
    }

    public function testInitializationEmptyString()
    {
        $command = new DTO(['property' => '']);

        self::assertNull($command->property);
    }
}
