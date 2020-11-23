<?php
/**
 * @class DbConnection
 *
 * This class its used to connect to the MySQL database.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Db_Connection extends Singleton
{

    private $pdo = null;

    /**
     * Initialize a connection
     */
    protected function initialize()
    {
        $this->pdo = null;
        try {
            $pdoDsn = 'mysql:host=' . ASTERION_DB_SERVER . ';port=' . ASTERION_DB_PORT . ';dbname=' . ASTERION_DB_NAME;
            $this->pdo = new PDO($pdoDsn, ASTERION_DB_USER, ASTERION_DB_PASSWORD, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
        } catch (PDOException $error) {
            if (ASTERION_DEBUG) {
                throw new Exception('<pre>' . $error->getMessage() . '</pre>');
            }
        }
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    /**
     * Test the connection to the database
     */
    static public function testConnection()
    {
        try {
            $pdoDsn = 'mysql:host=' . ASTERION_DB_SERVER . ';port=' . ASTERION_DB_PORT . ';dbname=' . ASTERION_DB_NAME;
            $pdo = new PDO($pdoDsn, ASTERION_DB_USER, ASTERION_DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            return true;
        } catch (PDOException $ex) {
            return false;
        }
    }

    /**
     * Execute a query
     */
    public function execute($query, $values = [])
    {
        try {
            $this->pdo->beginTransaction();
            $prepare_execute = $this->getPDOStatement($query);
            $prepare_execute->execute($values);
            $this->pdo->commit();
        } catch (PDOException $error) {
            if (ASTERION_DEBUG) {
                throw new Exception('<pre>' . $error->getMessage() . '</pre>');
            }
        }
    }

    /**
     * Get the PDO statement
     */
    public function getPDOStatement($query)
    {
        try {
            return $this->pdo->prepare($query, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        } catch (PDOException $error) {
            if (ASTERION_DEBUG) {
                throw new Exception('<pre>' . $error->getMessage() . '</pre>');
            }
        }
    }

}
