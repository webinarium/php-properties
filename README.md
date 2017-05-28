[![PHP](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)](https://secure.php.net/migration70)
[![Latest Stable Version](https://poser.pugx.org/webinarium/php-properties/v/stable)](https://packagist.org/packages/webinarium/php-properties)
[![Build Status](https://travis-ci.org/webinarium/php-properties.svg?branch=master)](https://travis-ci.org/webinarium/php-properties)
[![Code Coverage](https://scrutinizer-ci.com/g/webinarium/php-properties/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webinarium/php-properties/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webinarium/php-properties/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webinarium/php-properties/?branch=master)

# Automatic properties implementation for PHP

## Tl;dr

The library provides a trait to simulate automatic/custom properties as they are defined in C#.

## Installation

The recommended way to install is via Composer:

```bash
composer require webinarium/php-properties
```

## The Problem

Let's assume we need a class to represent a user's entity (quite popular case in the webdev world). The class must provide read-only ID, writeable first and last names, and a helper function to get the full name compound from the first and last ones:

```php
class User
{
    protected $id;
    protected $firstName;
    protected $lastName;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
```

Only three properties and one extra function to get the full name, but our class is already bloated with a lot of getters and setters.

Meanwhile in C# the same class could be implemented as following:

```cs
public class User
{
    public int Id { get; }
    public string FirstName { get; set; }
    public string LastName { get; set; }
    public string FullName {
        get {
            return FirstName + " " + LastName;
        }
    }
}
```

Nice and easy - even if you don't know C# you still understand what this code says. How can we get the same in PHP?

## Magic methods

Of course, the first thought is magic methods, so we can rewrite the class as following:

```php
/**
 * @property-read int    $id
 * @property      string $firstName
 * @property      string $lastName
 * @property-read string $fullName
 */
class User
{
    protected $id;
    protected $firstName;
    protected $lastName;

    public function __isset($name)
    {
        if ($name === 'fullName') {
            return true;
        }

        return property_exists($this, $name);
    }

    public function __get($name)
    {
        if ($name === 'fullName') {
            return $this->firstName . ' ' . $this->lastName;
        }

        return property_exists($this, $name)
            ? $this->$name
            : null;
    }

    public function __set($name, $value)
    {
        if ($name === 'id') {
            return;
        }

        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
}
```

Well, it works, but it takes nearly the same amount of code as the original class. Assume more properties, where some are read-only as `Id` and some a "virtual" as `fullName` and you will end up with long `switch` operators in all three magic functions. Also, I bet your IDE doesn't autocomplete these properties, so we have to append the class with `@property` annotations.

## Annotations

We can't change the PHP syntax, but we still can extend it using annotations. Really, if we had to write the annotations in the above example, why not reuse them instead of update all three magic functions each time we introduce a new property. And this is exactly what this library does, providing required functionality in the `PropertyTrait`.

If you include the `PropertyTrait` in your class, the `@property` annotations become a required declarion regarding your properties. Let's refactor our class using the trait:

```php
/**
 * @property-read  int    $id
 * @property       string $firstName
 * @property       string $lastName
 * @property-read  string $fullName
 */
class User
{
    use PropertyTrait;

    protected $id;
    protected $firstName;
    protected $lastName;

    protected function getters(): array
    {
        return [
            'fullName' => function () {
                return $this->firstName . ' ' . $this->lastName;
            },
        ];
    }
}
```

Maybe still not as elegant as the C# version, but much closer, isn't it?

## Automatic properties

Using `@property` annotation you can expose any existing protected or private property. To make a property read-only (or write-only) use a `@property-read` (or `@property-write`) annotation instead. If you don't specify a `@property` annotation for some existing property, it will remain hidden.

## Custom (virtual) properties

The trait contains two protected functions - `getters` and `setters` - which can be overridden in your class. Both functions return associated array of anonymous functions, and keys of the array are names of your virtual properties.

Let's assume we want to store some user-specific settings like user's language and user's timezone. We might have a lot of such configuration options and we don't want to bloat the related database table with thte same amount of columns, while all these settings can be stored in a single `settings` array:

```php
/**
 * ...
 * @property string $language
 * @property string $timezone
 */
class User
{
    use PropertyTrait;

    ...
    protected $settings;

    protected function getters(): array
    {
        return [

            'language' => function () {
                return $this->settings['language'] ?? 'en';
            },

            'timezone' => function () {
                return $this->settings['timezone'] ?? 'UTC';
            },
        ];
    }

    protected function setters(): array
    {
        return [

            'language' => function ($value) {
                $this->settings['language'] = $value;
            },

            'timezone' => function ($value) {
                $this->settings['timezone'] = $value;
            },
        ];
    }
}
```

Actually, you can provide your custom getters and setters via the `getters`/`setters` functions for existing property, too. In this case you will override the default behaviour of the property.

## Performance

Annotations are expensive. To work around this the trait caches parsed annotations in memory, so they are parsed only once (per web-request). Below is a table of few benchmarks for different ways to work with class properties. Each number is amount of seconds which took to read a property 100000 (one hundred thousand) times. Number are calculated from 5 sequental runs.

| Method to read a property | Min time | Avg time | Max time |
|:-------------------------------- | --------:| --------:| --------:|
| Direct access to public property | 0.007 | 0.008 | 0.009 | 
| Classic getter | 0.065 | 0.066 | 0.067 |
| Magic `__get` function | 0.073 | 0.074 | 0.075 |
| Using `PropertyTrait` | 0.173 | 0.175 | 0.177 |

## Development

```bash
./bin/php-cs-fixer fix
./bin/phpunit --coverage-text
```
