<?php

namespace SparkPost\Test\TestUtils;

class ClassUtils
{
    private $class;

    public function __construct($fqClassName)
    {
        $this->class = new \ReflectionClass($fqClassName);
    }

    /**
     * Allows access to private methods.
     *
     * This is needed to mock the GuzzleHttp\Client responses
     *
     * @param string $name
     *
     * @return ReflectionMethod
     */
    public function getMethod($method)
    {
        $method = $this->class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Allows access to private properties in the Transmission class.
     *
     * This is needed to mock the GuzzleHttp\Client responses
     *
     * @param string $name
     * @param {*}
     *
     * @return ReflectionMethod
     */
    public function getProperty($instance, $property)
    {
        $prop = $this->class->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($instance);
    }
    /**
     * Allows access to private properties in the Transmission class.
     *
     * This is needed to mock the GuzzleHttp\Client responses
     *
     * @param string $name
     * @param {*}
     *
     * @return ReflectionMethod
     */
    public function setProperty($instance, $property, $value)
    {
        $prop = $this->class->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($instance, $value);
    }
}
