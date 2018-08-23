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
                        <div class="infoUserItem">
                            <a href="'.url('User/myAccount', true).'">
                                <i class="icon icon-user"></i>
                                <span>'.__('myAccount').'</span>
                            </a>
                        </div>
                        <div class="infoUserLogout">
                            <a href="'.url('User/logout', true).'">
                                <i class="icon icon-power-button"></i>
                            </a>
                        </div>
                    </div>';
        }
    }

}

?>