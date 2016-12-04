<?php
/**
* @class HtmlSectionAdminUi
*
* This class manages the UI for the HtmlSectionAdmin objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class HtmlSectionAdmin_Ui extends Ui{

	/**
    * Render an HTML section
    */
    public function renderPublic() {
        return '<div class="pageComplete">'.$this->object->get('section').'</div>';
    }

}

?>