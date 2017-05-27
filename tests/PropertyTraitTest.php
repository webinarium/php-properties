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

require_once __DIR__ . '/EmptyClass.php';
require_once __DIR__ . '/User.php';

class PropertyTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testReadWriteExistingProperty()
    {
        $user = new User();
        self::assertNull($user->firstName);

        $user->firstName = 'Anna';
        self::assertEquals('Anna', $user->firstName);
    }

    public function testReadOnlyExistingProperty()
    {
        $user = new User();
        self::assertNull($user->id);

        $reflection = new \ReflectionProperty($user, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($user, 1234);

        self::assertEquals(1234, $user->id);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown write property: id
     */
    public function testReadOnlyExistingPropertyException()
    {
        $user = new User();

        $user->id = 1234;
    }

    public function testWriteOnlyExistingProperty()
    {
        $user = new User();

        $reflection = new \ReflectionProperty($user, 'password');
        $reflection->setAccessible(true);

        self::assertNull($reflection->getValue($user));

        $user->password = 'secret';

        self::assertEquals(md5('secret'), $reflection->getValue($user));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown read property: password
     */
    public function testWriteOnlyExistingPropertyException()
    {
        $user = new User();

        $password = $user->password;
    }

    public function testReadWriteVirtualProperty()
    {
        $user = new User();
        self::assertEquals('en', $user->locale);

        $user->locale = 'ru';
        self::assertEquals('ru', $user->locale);
    }

    public function testReadOnlyVirtualProperty()
    {
        $user = new User();
        self::assertEmpty(trim($user->fullName));

        $user->firstName = 'Anna';
        $user->lastName  = 'Rodygina';
        self::assertEquals('Anna Rodygina', $user->fullName);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown write property: fullName
     */
    public function testReadOnlyVirtualPropertyException()
    {
        $user = new User();

        $user->fullName = 'Anna Rodygina';
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown read property: age
     */
    public function testReadUnknownPropertyException()
    {
        $user = new User();

        $age = $user->age;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown write property: age
     */
    public function testWriteUnknownPropertyException()
    {
        $user = new User();

        $user->age = 12;
    }

    public function testEmptyClassGetters()
    {
        $empty = new EmptyClass();

        $reflection = new \ReflectionMethod($empty, 'getters');
        $reflection->setAccessible(true);

        self::assertCount(0, $reflection->invoke($empty));
  }

    public function testEmptyClassSetters()
    {
        $empty = new EmptyClass();

        $reflection = new \ReflectionMethod($empty, 'setters');
        $reflection->setAccessible(true);

        self::assertCount(0, $reflection->invoke($empty));
  }
}
