<?php
/**
 * @class UserAdminLogin
 *
 * This class manages the login UserAdmin objects.
 * It is a singleton, so it can only be instantiated one object using a function.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class UserAdmin_Login
{

    /**
     * Singleton pattern.
     * To instantiate this object we use the getInstance() static function.
     */
    protected static $login = null;
    protected $info;

    private function __construct()
    {
        $this->info = Session::get('info_user_admin');
        $this->info = ($this->info == '') ? [] : $this->info;
    }

    private function __clone()
    {}

    public static function getInstance()
    {
        if (null === self::$login) {
            self::$login = new self();
        }
        return self::$login;
    }

    /**
     * Get the id of the logged user admin.
     */
    public function id()
    {
        return $this->get('id');
    }

    /**
     * Universal getter.
     */
    public function get($value)
    {
        return (isset($this->info[$value])) ? $this->info[$value] : '';
    }

    /**
     * Get the user admin.
     */
    public function userAdmin()
    {
        if (isset($this->userAdmin)) {
            return $this->userAdmin;
        }
        $this->userAdmin = (new UserAdmin)->read($this->id());
        return $this->userAdmin;
    }

    /**
     * Update the session array.
     */
    private function sessionAdjust($info = [])
    {
        Session::set('info_user_admin', $info);
    }

    /**
     * Check if the user admin is connected.
     */
    public function isConnected()
    {
        return (isset($this->info['id']) && $this->info['id'] != '') ? true : false;
    }

    /**
     * Check the user admin login using it's email and password.
     * If so, it saves the user admin values in the session.
     */
    public function checklogin($options)
    {
        $values = [];
        $values['email'] = (isset($options['email'])) ? $options['email'] : '';
        $values['password'] = (isset($options['password'])) ? $options['password'] : '';
        $values['hashed_password'] = hash('sha256', $values['password']);
        if ($values['email'] != '' && $values['password'] != '') {
            $userAdmin = (new UserAdmin)->readFirst(['where' => 'email=:email AND (password=:hashed_password OR temporary_password=:password) AND active="1"'], $values);
            if ($userAdmin->id() != '') {
                $this->autoLogin($userAdmin);
                $this->sessionAdjust($this->info);
                return true;
            }
        }
        return false;
    }

    /**
     * Automatically login a user admin.
     */
    public function autoLogin($userAdmin)
    {
        $type = (new UserAdminType)->read($userAdmin->get('id_user_admin_type'));
        $this->info['id'] = $userAdmin->id();
        $this->info['email'] = $userAdmin->get('email');
        $this->info['label'] = $userAdmin->getBasicInfo();
        $this->info['code'] = $type->get('code');
        $this->info['id_user_admin_type'] = $type->id();
        $this->sessionAdjust($this->info);
    }

    /**
     * Eliminate session values and logout a user admin.
     */
    public function logout()
    {
        $this->info = [];
        $this->sessionAdjust();
    }

}
