<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017-2020 Artem Rodygin
//
//  You should have received a copy of the MIT License along with
//  this file. If not, see <http://opensource.org/licenses/MIT>.
//
//----------------------------------------------------------------------

namespace Webinarium;

/**
 * Trait to initialize Data Transfer Objects right on creation.
 */
trait DataTransferObjectTrait
{
    /**
     * Initializes object properties with values from provided array.
     *
     * @param array $values Initial values.
     */
    public function __construct(?array $values = null)
    {
        $data = $values ?? [];

        $properties = array_keys(get_class_vars(static::class));

        foreach ($properties as $property) {
            if (array_key_exists($property, $data)) {
                $this->{$property} = $data[$property];
            }
        }
    }
}
