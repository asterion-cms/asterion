<?php
/**
* @class Db
*
* This class has all the static methods to communicate and execute queries in the MySQL database.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Db {
    
    /**
    * Execute a query
    */
    static public function execute($query, $values=array()) {
        $query = str_replace('\"', '"', $query);
        $db = Db_Connection::getInstance();
        $db->execute($query, $values);
    }

    /**
    * Execute a query in a direct way
    */
    static public function executeSimple($query, $values=array()) {
        $db = Db_Connection::getInstance();
        $db->execute($query, $values);
    }

    /**
    * Return a single element
    */
    static public function returnSingle($query) {
        $query = str_replace('\"', '"', $query);
        $db = Db_Connection::getInstance();
        $prepare_execute = $db->getPDOStatement($query);
        $prepare_execute->execute();
        return $prepare_execute->fetch(PDO::FETCH_ASSOC);
    }

    /**
    * Return a list of elements
    */
    static public function returnAll($query) {
        $query = str_replace('\"', '"', $query);
        $db = Db_Connection::getInstance();
        $prepare_execute = $db->getPDOStatement($query);
        $prepare_execute->execute();
        return $prepare_execute->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    * Return a list of columns
    */
    static public function returnAllColumn($query) {
        $query = str_replace('\"', '"', $query);
        $db = Db_Connection::getInstance();
        $prepare_execute = $db->getPDOStatement($query);
        $prepare_execute->execute();
        return $prepare_execute->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
    * Describe a table
    */
    static public function describe($table) {
        return Db::returnSingle('DESCRIBE '.Db::prefixTable($table).';');
    }

    /**
    * Check if a table exists
    */
    static public function tableExists($table) {
        try {
            Db::describe($table);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
    * Create the table if not exists already
    */
    static public function initTable($table) {
        foreach (explode(',', $table) as $objectName) {
            $objectName = trim($objectName);
            if (!Db::returnSingle('SHOW TABLES LIKE "'.Db::prefixTable($objectName).'";')) {
                $object = new $objectName();
                $object->createTable();
            }
        }
    }

    /**
    * Prefix a set of tables
    */
    static public function prefixTable($table) {
        $result = array();
        foreach (explode(',', $table) as $objectName) {
            array_push($result, DB_PREFIX.trim($objectName));
        }
        return implode(',',$result) ;
    }
    
}
?>