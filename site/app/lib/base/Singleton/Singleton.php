<?php
/**
 * @class Singleton
 *
 * This is an abstract class for the singleton pattern.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
abstract class Singleton
{

    private static $instances = [];

    final private function __construct()
    {
        $class = get_called_class();
        if (array_key_exists($class, self::$instances) && ASTERION_DEBUG) {
            throw new Exception('An instance of ' . $calledClass . ' already exists !');
        }
        $this->initialize();
    }
    final private function __clone()
    {}

    abstract protected function initialize();

    /**
     * Static function to retrieve an instance of the object.
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (array_key_exists($class, self::$instances) === false) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }
}
