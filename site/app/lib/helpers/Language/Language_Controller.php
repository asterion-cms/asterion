<?php
/**
 * @class LanguageController
 *
 * This class is the controller for the Language objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Language_Controller extends Controller
{

    public function getContent()
    {
        switch ($this->action) {
            default:
                return parent::getContent();
                break;
            case 'insert':
                /**
                 * When we change a language we need to fix the redirection to the list.
                 */
                $this->checkLoginAdmin();
                $insert = $this->insert();
                if ($insert['success'] == '1') {
                    header('Location: ' . url($this->type, true));
                    exit();
                } else {
                    return parent::getContent();
                }
                break;
        }
    }

}
