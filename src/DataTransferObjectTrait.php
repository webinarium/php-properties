<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017 Artem Rodygin
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
    public function __construct(array $values = null)
    {
        /**
         * Replaces empty strings with nulls.
         *
         * @param mixed $value A value to be updated. Can be an array.
         *
         * @return mixed Updated value.
         */
        $empty2null = function ($value) use (&$empty2null) {

            if (is_array($value)) {
                return array_map($empty2null, $value);
            }

            return is_string($value) && mb_strlen($value) === 0 ? null : $value;
        };

        $data = $empty2null($values ?? []);

        $properties = array_keys(get_object_vars($this));

        foreach ($properties as $property) {
            if (array_key_exists($property, $data)) {
                $this->$property = $data[$property];
            }
        }
    }
}
