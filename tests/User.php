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

    protected int     $id;
    protected string  $firstName;
    protected string  $lastName;
    protected ?string $password = null;
    protected array   $settings = [];

    protected function getters(): array
    {
        return [
            'fullName' => fn () => ($this->firstName ?? null) . ' ' . ($this->lastName ?? null),
            'locale'   => fn () => $this->settings['locale'] ?? 'en',
            'timezone' => fn () => $this->settings['timezone'] ?? null,
        ];
    }

    protected function setters(): array
    {
        return [

            'password' => function (string $value): void {
                $this->password = md5($value);
            },

            'locale' => function (string $value): void {
                $this->settings['locale'] = $value;
            },

            'timezone' => function (string $value): void {
                $this->settings['timezone'] = $value;
            },
        ];
    }
}
