<?php
/**
 * @class NavigationController
 *
 * This is the controller for all the public actions of the website.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Navigation_Controller extends Controller
{

    /**
     * The constructor of the object.
     */
    public function __construct($GET, $POST, $FILES)
    {
        parent::__construct($GET, $POST, $FILES);
        $this->ui = new Navigation_Ui($this);
    }

    /**
     * Main function to control the public actions.
     */
    public function getContent()
    {
        switch ($this->action) {
            default:
            case 'error':
                header("HTTP/1.0 404 Not Found");
                $this->titlePage = 'Error 404';
                $this->content = 'Error 404';
                return $this->ui->render();
                break;
            case 'intro':
                $this->title_page = Params::param('title_page');
                $this->meta_url = url('');
                $this->meta_description = 'The installation of your Asterion website is complete';
                $this->meta_keywords = 'asterion, installation, website';
                $this->content = '<div class="page_complete">
                                        <h1>Congratulations !</h1>
                                        <p>The installation is complete and you have a proper Asterion website now.</p>
                                        <p>From now on you can start coding your website. You can always <a href="https://www.asterion-cms.com/documentation" target="_blank">find the documentation here</a>.</p>
                                    </div>';
                return $this->ui->render();
                break;
        }
    }

}
