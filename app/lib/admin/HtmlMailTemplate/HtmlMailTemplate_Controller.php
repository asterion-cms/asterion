<?php
/**
* @class HtmlMailTemplateController
*
* This class is the controller for the HtmlMailTemplate objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class HtmlMailTemplate_Controller extends Controller {

    /**
    * Overwrite the listAdmin function of this controller.
    */
    public function listAdmin() {
        $item = HtmlMailTemplate::readFirst();
        if ($item->id()!='') {
            header('Location: '.url('HtmlMailTemplate/modifyView/'.$item->id(), true));
        } else {
            return parent::controlActions();
        }
    }

    /**
    * Overwrite the modifyView function of this controller.
    */
    public function modifyView() {
        $this->menuInside = '';
        return parent::modifyView();
    }
    
}
?>