<?php
/**
* @class Singleton
*
* This is an abstract class for the singleton pattern.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
abstract class Singleton {
    
    private static $instances = array();
    
    final private function __construct() {
        $class = get_called_class();
        if (array_key_exists($class, self::$instances) && DEBUG) {
            throw new Exception('An instance of '. $calledClass .' already exists !');
        }
        $this->initialize(); 
    }
    final private function __clone() { }
    
    abstract protected function initialize();
    
    /**
    * Static function to retrieve an instance of the object.
    */
    static public function getInstance() {
        $class = get_called_class();
        if (array_key_exists($class, self::$instances) === false)
        self::$instances[$class] = new $class();
        return self::$instances[$class];
    }
}
?>