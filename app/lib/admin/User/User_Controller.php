<?php
/**
* @class UserController
*
* This class is the controller for the User objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class User_Controller extends Controller {

    public function controlActions(){
        $this->ui = new NavigationAdmin_Ui($this);
        switch ($this->action) {
            case 'login':
                $this->mode = 'admin';
                $this->layoutPage = 'simple';
                $this->titlePage = __('login');
                if (count($this->values)>0) {
                    $login = User_Login::getInstance();
                    $login->checklogin($this->values);
                    if ($login->isConnected()) {
                        header('Location: '.url('NavigationAdmin/intro', true));
                    } else {
                        $form = new User_Form();
                        $this->messageError = __('errorConnection');
                        $this->content = $form->loginAdmin();
                    }
                } else {                
                    $form = new User_Form();
                    $this->content = $form->loginAdmin();
                }
                return $this->ui->render();
            break;
            case 'logout':
                $login = User_Login::getInstance();
                $login->logout();
                header('Location: '.url('', true));
            break;
            case 'forgot':
                $this->mode = 'admin';
                $this->layoutPage = 'simple';
                $this->titlePage = __('passwordForgot');
                $form = new User_Form();
                if (isset($this->values['email'])) {
                    $user = User::readFirst(array('where'=>'email="'.$this->values['email'].'" AND active="1"'));
                    if ($user->id()!='') {
                        $tempPassword = substr(md5(rand()*rand()), 0, 10);
                        $user->modifySimple('passwordTemp', $tempPassword);
                        $updatePasswordLink = url('User/login', true);
                        HtmlMail::send($user->get('email'), 'passwordForgot', array('TEMP_PASSWORD'=>$tempPassword, 'UPDATE_PASSWORD_LINK'=>$updatePasswordLink));
                        $this->content = $form->forgotSent();
                    } else {
                        $this->messageError = __('mailDoesntExist');
                        $this->content = $form->forgot();
                    }
                } else {
                    $form = new User_Form();
                    $this->content = $form->forgot();
                }
                return $this->ui->render();
            break;
            case 'myAccount':
                $this->login = User::loginAdmin();
                $this->mode = 'admin';
                $this->titlePage = __('myAccount');
                $form = User_Form::newObject($this->login->user());
                $this->message = ($this->id == 'successPersonal') ? __('savedForm') : '';
                $this->message = ($this->id == 'successPassword') ? __('changePasswordSuccess') : $this->message;
                $this->messageError = ($this->id == 'errorPersonal') ? __('errorsForm') : '';
                $this->messageError = ($this->id == 'errorPassword') ? __('changePasswordError') : $this->messageError;
                $this->content = '<div class="myAccount">
                                        <div class="myAccountPersonal">
                                            '.$form->myAccount().'
                                        </div>
                                        <div class="myAccountPassword">
                                            '.$form->changePassword().'
                                        </div>
                                    </div>';
                return $this->ui->render();
            break;
            case 'myAccountPersonal':
                $this->login = User::loginAdmin();
                if (count($this->values) > 0) {
                    $this->values['idUser'] = $this->login->id();
                    $form = new User_Form($this->values);
                    $errors = $form->isValidMyInformation($this->values);
                    if (count($errors) > 0) {
                        header('Location: '.url('User/myAccount/errorPersonal', true));
                    } else {
                        $this->login->user()->modify($this->values, array('complete'=>false));
                        header('Location: '.url('User/myAccount/successPersonal', true));
                    }
                    exit();
                }
                header('Location: '.url('User/myAccount', true));
                exit();
            break;
            case 'myAccountPassword':
                $this->login = User::loginAdmin();
                if (count($this->values) > 0) {
                    $this->values['idUser'] = $this->login->id();
                    $form = new User_Form($this->values);
                    $errors = $form->isValidChangePassword($this->login->user());
                    if (count($errors) > 0) {
                        header('Location: '.url('User/myAccount/errorPassword', true));
                    } else {
                        $this->login->user()->modify($this->values, array('complete'=>false));
                        $this->login->user()->modifySimple('passwordTemp', '');
                        header('Location: '.url('User/myAccount/successPassword', true));
                    }
                    exit();
                }
                header('Location: '.url('User/myAccount', true));
                exit();
            break;
        }
        return parent::controlActions();
    }    
}
?>