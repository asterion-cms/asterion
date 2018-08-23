<?php
/**
* @class LangController
*
* This class is the controller for the Lang objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Lang_Controller extends Controller {

	public function controlActions(){
        switch ($this->action) {
            default:
            	return parent::controlActions();
            break;
            case 'insert':
                /**
                * When we change a language we need to fix the redirection to the list.
                */
                $this->checkLoginAdmin();
                $insert = $this->insert();
                if ($insert['success']=='1') {
                    header('Location: '.url($this->type, true));
                    exit();
                } else {
                	return parent::controlActions();
                }
            break;
        }
    }

}
?>