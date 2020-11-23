<?php
/**
 * @class UserAdminUi
 *
 * This class manages the UI for the UserAdmin objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class UserAdmin_Ui extends Ui
{

    public static function infoHtml()
    {
        $login = UserAdmin_Login::getInstance();
        if ($login->isConnected()) {
            return '<div class="info_user">
                        <a href="' . url('user_admin/account', true) . '" class="info_user_item">
                            <i class="fa fa-user"></i>
                            <span>' . __('account') . '</span>
                        </a>
                        <a href="' . url('user_admin/logout', true) . '" class="info_user_logout">
                            <i class="fa fa-power-off"></i>
                        </a>
                    </div>';
        }
    }

}
