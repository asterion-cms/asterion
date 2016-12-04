<?php
/**
* @class NavigationController
*
* This is the controller for all the public actions of the website.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Navigation_Controller extends Controller{

    /**
    * The constructor of the object.
    */
    public function __construct($GET, $POST, $FILES) {
        parent::__construct($GET, $POST, $FILES);
        $this->ui = new Navigation_Ui($this);
    }
    
    /**
    * Main function to control the public actions.
    */
    public function controlActions(){
        switch ($this->action) {
            default:
            case 'error':
                header("HTTP/1.0 404 Not Found");
                $this->titlePage = 'Error 404';
                $this->content = 'Error 404';
                return $this->ui->render();
            break;
            case 'intro':
                $this->content = '<div class="pageComplete">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque et sapien at odio fringilla congue. Nullam rutrum mi eu finibus tincidunt. Curabitur auctor enim diam, in volutpat diam hendrerit id. Mauris auctor eros eu eleifend auctor. Nullam accumsan orci nisl, at egestas tortor scelerisque et. Donec sapien nunc, mollis ut vestibulum eget, malesuada in nisl. Duis suscipit sem vel enim placerat, quis consectetur ligula facilisis. Ut a elit ultricies, ornare nulla quis, pulvinar turpis.</p>
                                        <p>Curabitur scelerisque erat sed tellus interdum rutrum. Duis sagittis finibus felis ultrices laoreet. Pellentesque et lacinia tellus. Suspendisse id eros sed sem suscipit bibendum. Duis vulputate lacinia hendrerit. Phasellus pellentesque et leo at iaculis. Aliquam nec vulputate nisi. Ut ullamcorper lorem neque, pretium efficitur tortor porta et. Nam sit amet sagittis quam. Vestibulum vel mattis lectus. Quisque ante metus, cursus id scelerisque ultricies, bibendum non diam. Curabitur auctor, massa et condimentum pharetra, ligula lorem tincidunt sapien, vitae feugiat ipsum velit eget diam. Nullam tempus felis et enim ultricies auctor. Sed imperdiet sapien nec sem cursus, non tempus odio mollis. Mauris at odio sed ante consequat elementum faucibus quis urna. Suspendisse a lacus vitae est pulvinar congue quis et mauris.</p>
                                        <p>Suspendisse potenti. Aenean quis velit et orci elementum mattis et et enim. Duis et rhoncus elit, ut ornare nunc. Proin lobortis lacinia neque ut laoreet. Cras eget lacinia orci. Donec malesuada vehicula venenatis. Aenean eu congue justo. Curabitur faucibus ultrices feugiat. Maecenas a blandit sem.</p>
                                    </div>';
                return $this->ui->render();
            break;
        }
    }

}
?>