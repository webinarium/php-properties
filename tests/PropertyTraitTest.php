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

require_once __DIR__ . '/EmptyClass.php';
require_once __DIR__ . '/User.php';

/**
 * @coversDefaultClass \Webinarium\PropertyTrait
 */
class PropertyTraitTest extends TestCase
{
    /**
     * @covers ::__isset
     * @covers ::parseAnnotations
     */
    public function testIsSet()
    {
        $user = new User();
        self::assertEmpty($user->id);
    }

    /**
     * @covers ::__get
     * @covers ::__set
     * @covers ::getters
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testReadWriteExistingProperty()
    {
        $user = new User();
        self::assertNull($user->firstName);

        $user->firstName = 'Anna';
        self::assertSame('Anna', $user->firstName);
    }

    /**
     * @covers ::__get
     * @covers ::getters
     * @covers ::parseAnnotations
     */
    public function testReadOnlyExistingProperty()
    {
        $user = new User();
        self::assertNull($user->id);

        $reflection = new \ReflectionProperty($user, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($user, 1234);

        self::assertSame(1234, $user->id);
    }

    /**
     * @covers ::__set
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testReadOnlyExistingPropertyException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown write property: id');

        $user = new User();

        $user->id = 1234;
    }

    /**
     * @covers ::__set
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testWriteOnlyExistingProperty()
    {
        $user = new User();

        $reflection = new \ReflectionProperty($user, 'password');
        $reflection->setAccessible(true);

        self::assertNull($reflection->getValue($user));

        $user->password = 'secret';

        self::assertSame(md5('secret'), $reflection->getValue($user));
    }

    /**
     * @covers ::__get
     * @covers ::getters
     * @covers ::parseAnnotations
     */
    public function testWriteOnlyExistingPropertyException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown read property: password');

        $user = new User();

        $password = $user->password;
    }

    /**
     * @covers ::__get
     * @covers ::__set
     * @covers ::getters
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testReadWriteNullableProperty()
    {
        $user = new User();
        self::assertNull($user->timezone);

        $user->timezone = 'UTC';
        self::assertSame('UTC', $user->timezone);
    }

    /**
     * @covers ::__get
     * @covers ::__set
     * @covers ::getters
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testReadWriteVirtualProperty()
    {
        $user = new User();
        self::assertSame('en', $user->locale);

        $user->locale = 'ru';
        self::assertSame('ru', $user->locale);
    }

    /**
     * @covers ::__get
     * @covers ::__set
     * @covers ::getters
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testReadOnlyVirtualProperty()
    {
        $user = new User();
        self::assertEmpty(trim($user->fullName));

        $user->firstName = 'Anna';
        $user->lastName  = 'Rodygina';
        self::assertSame('Anna Rodygina', $user->fullName);
    }

    /**
     * @covers ::__set
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testReadOnlyVirtualPropertyException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown write property: fullName');

        $user = new User();

        $user->fullName = 'Anna Rodygina';
    }

    /**
     * @covers ::__get
     * @covers ::getters
     * @covers ::parseAnnotations
     */
    public function testReadUnknownPropertyException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown read property: age');

        $user = new User();

        $age = $user->age;
    }

    /**
     * @covers ::__set
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testWriteUnknownPropertyException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown write property: age');

        $user = new User();

        $user->age = 12;
    }

    /**
     * @covers ::getters
     * @covers ::parseAnnotations
     */
    public function testEmptyClassGetters()
    {
        $empty = new EmptyClass();

        $reflection = new \ReflectionMethod($empty, 'getters');
        $reflection->setAccessible(true);

        self::assertCount(0, $reflection->invoke($empty));
    }

    /**
     * @covers ::parseAnnotations
     * @covers ::setters
     */
    public function testEmptyClassSetters()
    {
        $empty = new EmptyClass();

        $reflection = new \ReflectionMethod($empty, 'setters');
        $reflection->setAccessible(true);

        self::assertCount(0, $reflection->invoke($empty));
    }
}
