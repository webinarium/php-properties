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

use Webinarium\PropertyTrait;

/**
 * @property-read  int         $id
 * @property       string      $firstName
 * @property       string      $lastName
 * @property-read  string      $fullName
 * @property-write string      $password
 * @property       string      $locale
 * @property       null|string $timezone
 */
class User
{
    use PropertyTrait;

    protected $id;
    protected $firstName;
    protected $lastName;
    protected $password;
    protected $settings;

    protected function getters(): array
    {
        return [

            'fullName' => function () {
                return $this->firstName . ' ' . $this->lastName;
            },

            'locale' => function () {
                return $this->settings['locale'] ?? 'en';
            },

            'timezone' => function () {
                return $this->settings['timezone'] ?? null;
            },
        ];
    }

    protected function setters(): array
    {
        return [

            'password' => function ($value) {
                $this->password = md5($value);
            },

            'locale' => function ($value) {
                $this->settings['locale'] = $value;
            },

            'timezone' => function ($value) {
                $this->settings['timezone'] = $value;
            },
        ];
    }
}
