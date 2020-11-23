<?php
/**
 * @class Permission
 *
 * This class represents the permissions for objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Permission extends Db_Object
{

    /**
     * Function to check if the logged user has an specific permission on an object.
     */
    public static function getPermission($permissionCheck, $objectName)
    {
        $login = UserAdmin_Login::getInstance();
        if ($login->isConnected()) {
            $user_admin_type = (new UserAdminType)->read($login->get('id_user_admin_type'));
            if ($user_admin_type->get('manages_permissions') == '1') {
                return true;
            }
            $permission = (new Permission)->readFirst(['where' => 'object_name="' . $objectName . '" AND id_user_admin_type="' . $user_admin_type->id() . '" AND ' . $permissionCheck . '="1"']);
            return ($permission->id() != '');
        }
        return false;
    }

    /**
     * Function to check if the logged user can list items.
     */
    public static function canListAdmin($objectName)
    {
        return Permission::getPermission('permission_list_admin', $objectName);
    }

    /**
     * Function to check if the logged user can list items.
     */
    public static function canInsert($objectName)
    {
        return Permission::getPermission('permission_insert', $objectName);
    }

    /**
     * Function to check if the logged user can list items.
     */
    public static function canModify($objectName)
    {
        return Permission::getPermission('permission_modify', $objectName);
    }

    /**
     * Function to check if the logged user can list items.
     */
    public static function canDelete($objectName)
    {
        return Permission::getPermission('permission_delete', $objectName);
    }

    /**
     * Function to check all the permissions for the logged user on an object.
     */
    public static function getAll($objectName)
    {
        $login = UserAdmin_Login::getInstance();
        if ($login->isConnected()) {
            $user_admin_type = (new UserAdminType)->read($login->get('id_user_admin_type'));
            if ($user_admin_type->get('manages_permissions') == '1') {
                return ['permission_list_admin' => 1,
                    'permission_insert' => 1,
                    'permission_modify' => 1,
                    'permission_delete' => 1];
            }
            $permission = (new Permission)->readFirst(['where' => 'object_name="' . $objectName . '" AND id_user_admin_type="' . $user_admin_type->id() . '"']);
            return ['permission_list_admin' => $permission->get('permission_list_admin'),
                'permission_insert' => $permission->get('permission_insert'),
                'permission_modify' => $permission->get('permission_modify'),
                'permission_delete' => $permission->get('permission_delete')];
        }
        return ['permission_list_admin' => 0,
            'permission_insert' => 0,
            'permission_modify' => 0,
            'permission_delete' => 0];
    }

}
