<?php
/**
* @class UserTypeMenuUi
*
* This class manages the UI for the UserTypeMenu objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class UserTypeMenu_Ui extends Ui{

    /**
    * Render the menu item
    */
    public function renderMenu() {
        $content = ($this->object->get('action')!='') ? '<a href="'.url($this->object->get('action'), true).'">'.$this->object->get('name').'</a>' : '<span>'.$this->object->get('name').'</span>';
        return '<div class="menuSideItem menuSideItem-'.Text::simpleUrl($this->object->get('name')).'">
                    '.$content.'
                </div>';
    }

}
?>