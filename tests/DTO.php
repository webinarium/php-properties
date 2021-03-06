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

use Webinarium\DataTransferObjectTrait;

class DTO
{
    use DataTransferObjectTrait;

    public int $property = 1;
}
