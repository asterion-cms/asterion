<?php
/**
* @class UserUi
*
* This class manages the UI for the User objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class User_Ui extends Ui{

    static public function infoHtml() {
        $login = User_Login::getInstance();
        if ($login->isConnected()) {
            return '<div class="infoUser">
                        <div class="infoUserItem infoUserMyAccount">
                            <a href="'.url('User/myAccount', true).'">'.__('myAccount').'</a>
                        </div>
                        <div class="infoUserItem infoUserLogout">
                            <a href="'.url('User/logout', true).'">'.__('logout').'</a>
                        </div>
                    </div>';
        }
    }

}

?>