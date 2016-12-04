<?php
/**
* @class DbConnection
*
* This class its used to connect to the MySQL database.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Db_Connection extends Singleton {

    private $pdo=null;

    /**
    * Initialize a connection
    */
    protected function initialize(){
        $this->pdo = null;
        try{
            $this->pdo = new PDO(PDO_DSN, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        } catch(PDOException $error){
            if (DEBUG) {
                throw new Exception('<pre>'.$error->getMessage().'</pre>');
            }
        }
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    /**
    * Execute a query
    */
    public function execute($query, $values=array()){
        try {
            $this->pdo->beginTransaction();
            $prepare_execute = $this->getPDOStatement($query);
            $prepare_execute->execute($values);
            $this->pdo->commit();
        } catch(PDOException $error){
            if (DEBUG) {
                throw new Exception('<pre>'.$error->getMessage().'</pre>');
            }
        }
    }

    /**
    * Get the PDO statement
    */
    public function getPDOStatement($query){
        try {
            return $this->pdo->prepare($query,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        } catch(PDOException $error){
            if (DEBUG) {
                throw new Exception('<pre>'.$error->getMessage().'</pre>');
            }
        }
    }

}
?>