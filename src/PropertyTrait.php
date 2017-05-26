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
 * Trait to emulate automatic properties.
 */
trait PropertyTrait
{
    /** @var array Cached annotations. */
    private static $_annotations;

    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        if (self::$_annotations === null) {
            $this->parseAnnotations();
        }

        return self::$_annotations[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        if (self::$_annotations === null) {
            $this->parseAnnotations();
        }

        $access = self::$_annotations[$name] ?? null;

        if ($access !== 'property' && $access !== 'property-read') {
            throw new \InvalidArgumentException('Unknown read property: ' . $name);
        }

        $getters = $this->getters();

        return isset($getters[$name])
            ? $getters[$name]()
            : $this->$name;
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        if (self::$_annotations === null) {
            $this->parseAnnotations();
        }

        $access = self::$_annotations[$name] ?? null;

        if ($access !== 'property' && $access !== 'property-write') {
            throw new \InvalidArgumentException('Unknown write property: ' . $name);
        }

        $setters = $this->setters();

        isset($setters[$name])
            ? $setters[$name]($value)
            : $this->$name = $value;
    }

    /**
     * Returns array of custom getters.
     *
     * @return array
     */
    protected function getters(): array
    {
        return [];
    }

    /**
     * Returns array of custom setters.
     *
     * @return array
     */
    protected function setters(): array
    {
        return [];
    }

    /**
     * Parses annotations of the class.
     */
    private function parseAnnotations()
    {
        self::$_annotations = [];

        $class  = new \ReflectionClass($this);
        $phpdoc = explode("\n", $class->getDocComment());

        foreach ($phpdoc as $line) {
            // pattern = "@property[-read|-write] type $identifier"
            if (preg_match('/@(property|property\-read|property\-write)\W+[A-Za-z][_A-Za-z\d]*\W+\$([A-Za-z][_A-Za-z\d]*)/', $line, $matches)) {
                self::$_annotations[$matches[2]] = $matches[1];
            }
        }
    }
}
