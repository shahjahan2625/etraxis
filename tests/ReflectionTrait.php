<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2018 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App;

/**
 * A trait to access protected parts of an object.
 */
trait ReflectionTrait
{
    /**
     * Calls specified protected method of the object.
     *
     * @param mixed  $object Target object
     * @param string $name   Name of the method
     * @param array  $args   Optional arguments to pass to the method call
     *
     * @return null|mixed Method result
     */
    public function callMethod($object, string $name, array $args = [])
    {
        try {
            $reflection = new \ReflectionMethod(get_class($object), $name);
            $reflection->setAccessible(true);

            return $reflection->invokeArgs($object, $args);
        }
        catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Sets specified protected property of the object.
     *
     * @param mixed  $object Target object
     * @param string $name   Name of the property
     * @param mixed  $value  New value to be set
     */
    public function setProperty($object, string $name, $value): void
    {
        try {
            $reflection = new \ReflectionProperty(get_class($object), $name);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $value);
        }
        catch (\ReflectionException $e) {
        }
    }

    /**
     * Gets specified protected property of the object.
     *
     * @param mixed  $object Target object
     * @param string $name   Name of the property
     *
     * @return mixed Current value of the property
     */
    public function getProperty($object, string $name)
    {
        try {
            $reflection = new \ReflectionProperty(get_class($object), $name);
            $reflection->setAccessible(true);

            return $reflection->getValue($object);
        }
        catch (\ReflectionException $e) {
            return null;
        }
    }
}
