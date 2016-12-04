<?php
/**
* @class PermissionController
*
* This class is the controller for the Permission objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Permission_Controller extends Controller {

	/**
    * Overwrite the listAdmin function of this controller.
    */
    public function listAdmin() {
		$html = '';
		$this->menuInside = '';
		$objectNames = File::scanDirectoryObjectsBase(); 
		$html .= $this->listObjects($objectNames, 'formPermissionBase');
		$objectNames = File::scanDirectoryObjectsApp(); 
		$html .= $this->listObjects($objectNames, 'formPermissionApp');
		return '<div class="formPermissions">
					'.Form::createForm($html, array('action'=>url('Permission/insert', true), 'submit'=>__('save'))).'
				</div>';
	}

	/**
    * List the objects and the permissions for each one.
    */
    public function listObjects($objectNames, $class='') {
    	$html = '';
    	$userTypes = UserType::readList(array('where'=>'managesPermissions!="1"'));
		foreach ($objectNames as $objectName) {
			$htmlPermissions = '';
			$object = new $objectName();
			foreach ($userTypes as $userType) {
				$permission = Permission::readFirst(array('where'=>'idUserType="'.$userType->id().'" AND objectName="'.$objectName.'"'));
				$htmlPermissions .= '<div class="formPermissionOption">
										<div class="formPermissionOptionUser">'.$userType->getBasicInfo().'</div>
										<div class="formPermissionOptionCheckboxes">
											'.FormField::create('checkbox', array('name'=>'permissionListAdmin_'.$userType->id().'_'.$objectName, 'label'=>'permissionListAdmin', 'value'=>$permission->get('permissionListAdmin'))).'
											'.FormField::create('checkbox', array('name'=>'permissionInsert_'.$userType->id().'_'.$objectName, 'label'=>'permissionInsert', 'value'=>$permission->get('permissionInsert'))).'
											'.FormField::create('checkbox', array('name'=>'permissionModify_'.$userType->id().'_'.$objectName, 'label'=>'permissionModify', 'value'=>$permission->get('permissionModify'))).'
											'.FormField::create('checkbox', array('name'=>'permissionDelete_'.$userType->id().'_'.$objectName, 'label'=>'permissionDelete', 'value'=>$permission->get('permissionDelete'))).'
										</div>
									</div>';
			}
			$html .= '<div class="formPermission '.$class.'">
							<div class="formPermissionObject">'.$objectName.'</div>
							<div class="formPermissionOptions">
								<div class="formPermissionOptionsIns">
									'.$htmlPermissions.'
								</div>
							</div>
						</div>';
		}
		return $html;
    }

	/**
    * Overwrite the insert function of this controller.
    */
	public function insert() {
		$objectNames = File::scanDirectoryObjects(); 
		$userTypes = UserType::readList();
		Db::execute('TRUNCATE '.Db::prefixTable('Permission'));
		foreach ($objectNames as $objectName) {
			foreach ($userTypes as $userType) {
				$permission = new Permission();
				$permission->insert(array('idUserType'=>$userType->id(), 'objectName'=>$objectName));
				if (isset($this->values['permissionListAdmin_'.$userType->id().'_'.$objectName])) {
					$permission->modifySimple('permissionListAdmin', 1);
				}
				if (isset($this->values['permissionInsert_'.$userType->id().'_'.$objectName])) {
					$permission->modifySimple('permissionInsert', 1);
				}
				if (isset($this->values['permissionModify_'.$userType->id().'_'.$objectName])) {
					$permission->modifySimple('permissionModify', 1);
				}
				if (isset($this->values['permissionDelete_'.$userType->id().'_'.$objectName])) {
					$permission->modifySimple('permissionDelete', 1);
				}
			}
		}
		header('Location: '.url('Permission/listAdmin', true));
	}

}
?>