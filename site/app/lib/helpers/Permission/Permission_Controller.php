<?php
/**
 * @class PermissionController
 *
 * This class is the controller for the Permission objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Permission_Controller extends Controller
{

    /**
     * Overwrite the list_admin function of this controller.
     */
    public function listAdmin()
    {
        $html = '';
        $this->menuInside = '';
        $objectNames = File::scanDirectoryObjectsBase();
        $html .= $this->listObjects($objectNames, 'form_permissions_base');
        $objectNames = File::scanDirectoryObjectsApp();
        $html .= $this->listObjects($objectNames, 'form_permissionsApp');
        return '<div class="form_permissionss">
                    ' . Form::createForm($html, ['action' => url('Permission/insert', true), 'submit' => __('save')]) . '
                </div>';
    }

    /**
     * List the objects and the permissions for each one.
     */
    public function listObjects($objectNames, $class = '')
    {
        $html = '';
        $user_admin_types = (new UserAdminType)->readList(['where' => 'manages_permissions!="1"']);
        foreach ($objectNames as $objectName) {
            $htmlPermissions = '';
            $object = new $objectName();
            foreach ($user_admin_types as $user_admin_type) {
                $permission = (new Permission)->readFirst(['where' => 'id_user_admin_type="' . $user_admin_type->id() . '" AND object_name="' . $objectName . '"']);
                $htmlPermissions .= '<div class="form_permissions_option">
                                        <div class="form_permissions_option_user">' . $user_admin_type->getBasicInfo() . '</div>
                                        <div class="form_permissions_option_checkboxes">
                                            ' . FormField::create('checkbox', ['name' => 'permission_list_admin_' . $user_admin_type->id() . '_' . $objectName, 'label' => 'permission_list_admin', 'value' => $permission->get('permission_list_admin')]) . '
                                            ' . FormField::create('checkbox', ['name' => 'permission_insert_' . $user_admin_type->id() . '_' . $objectName, 'label' => 'permission_insert', 'value' => $permission->get('permission_insert')]) . '
                                            ' . FormField::create('checkbox', ['name' => 'permission_modify_' . $user_admin_type->id() . '_' . $objectName, 'label' => 'permission_modify', 'value' => $permission->get('permission_modify')]) . '
                                            ' . FormField::create('checkbox', ['name' => 'permission_delete_' . $user_admin_type->id() . '_' . $objectName, 'label' => 'permission_delete', 'value' => $permission->get('permission_delete')]) . '
                                        </div>
                                    </div>';
            }
            $html .= '<div class="form_permissions ' . $class . '">
                            <div class="form_permissions_object">' . $objectName . '</div>
                            <div class="form_permissions_options">
                                <div class="form_permissions_options_ins">
                                    ' . $htmlPermissions . '
                                </div>
                            </div>
                        </div>';
        }
        return $html;
    }

    /**
     * Overwrite the insert function of this controller.
     */
    public function insert()
    {
        $objectNames = File::scanDirectoryObjects();
        $user_admin_types = (new UserAdminType)->readList();
        Db::execute('TRUNCATE ' . Db::prefixTable('permission'));
        foreach ($objectNames as $objectName) {
            foreach ($user_admin_types as $user_admin_type) {
                $permission = new Permission();
                $permission->insert(['id_user_admin_type' => $user_admin_type->id(), 'object_name' => $objectName]);
                if (isset($this->values['permission_list_admin_' . $user_admin_type->id() . '_' . $objectName])) {
                    $permission->modifySimple('permission_list_admin', 1);
                }
                if (isset($this->values['permission_insert_' . $user_admin_type->id() . '_' . $objectName])) {
                    $permission->modifySimple('permission_insert', 1);
                }
                if (isset($this->values['permission_modify_' . $user_admin_type->id() . '_' . $objectName])) {
                    $permission->modifySimple('permission_modify', 1);
                }
                if (isset($this->values['permission_delete_' . $user_admin_type->id() . '_' . $objectName])) {
                    $permission->modifySimple('permission_delete', 1);
                }
            }
        }
        header('Location: ' . url('Permission/list_admin', true));
    }

}
