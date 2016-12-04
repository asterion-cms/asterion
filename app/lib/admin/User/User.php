<?php
/**
* @class User
*
* This class defines the users in the administration system.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class User extends Db_Object {

	/**
    * Check if a user is connected in the administration area.
    */
	static public function loginAdmin() {
        $login = User_Login::getInstance();
        if (!$login->isConnected()) {
            header('Location: '.url('User/login', true));
            exit();
        }
        return $login;
	}

}
?>