<?php
/**
* @class Helper
*
* This is a helper to create HTML components.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
abstract class Helper {

    /**
    * Create and HTML accordion using a select-box.
    */
    static public function accordionSelect($select, $switchValue, $content) {
        return '<div class="accordion accordionSelect">
                    <div class="accordionTrigger" rel="'.$switchValue.'">'.$select.'</div>
                    <div class="accordionContent">'.$content.'</div>
                </div>';
    }

}
?>