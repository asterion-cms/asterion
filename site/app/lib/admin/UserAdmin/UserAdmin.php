<?php
/**
 * @class UserAdmin
 *
 * This class defines the users in the administration system.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class UserAdmin extends Db_Object
{

    /**
     * Check if a user admin is connected in the administration area.
     */
    public static function loginAdmin()
    {
        $login = UserAdmin_Login::getInstance();
        if (!$login->isConnected()) {
            header('Location: ' . url('user_admin/login', true));
            exit();
        }
        return $login;
    }

}
