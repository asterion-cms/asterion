<?php
/**
 * @class HtmlMailTemplateController
 *
 * This class is the controller for the HtmlMailTemplate objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class HtmlMailTemplate_Controller extends Controller
{

    /**
     * Overwrite the list_admin function of this controller.
     */
    public function listAdmin()
    {
        $item = (new HtmlMailTemplate)->readFirst();
        if ($item->id() != '') {
            header('Location: ' . url('html_mail_template/modify_view/' . $item->id(), true));
        } else {
            return parent::getContent();
        }
    }

    /**
     * Overwrite the modify_view function of this controller.
     */
    public function modify_view()
    {
        $this->menuInside = '';
        return parent::modify_view();
    }

}
