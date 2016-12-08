<?php
/**
* @class UserForm
*
* This class manages the forms for the User objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class User_Form extends Form{

    public function loginAdmin() {
        $fields = $this->field('email').'
                    '.$this->field('password');
        return '<div class="simpleForm">
                    <p>'.__('loginMessage').'</p>
                    '.Form::createForm($fields, array('action'=>url('User/login', true), 'class'=>'formAdmin', 'submit'=>__('send'))).'
                    <p><a href="'.url('User/forgot', true).'">'.__('passwordForgot').'</a></p>
                </div>';
    }

    public function forgot() {
        $fields = $this->field('email');
        return '<div class="simpleForm">
                    <p>'.__('passwordForgotMessage').'</p>
                    '.Form::createForm($fields, array('action'=>url('User/forgot', true), 'class'=>'formAdmin', 'submit'=>__('send'))).'
                    <p><a href="'.url('User/login', true).'">'.__('tryLoginAgain').'</a></p>
                </div>';
    }

    public function forgotSent() {
        return '<div class="simpleForm">
                    <div class="message">'.__('passwordSentMail').'</div>
                    <p><a href="'.url('User/login', true).'">'.__('tryLoginAgain').'</a></p>
                </div>';
    }

    public function updatePassword() {
        $fields = $this->field('email').'
                '.FormField_Password::create(array('label'=>__('password'), 'name'=>'password', 'error'=>$this->errors['oldPassword'], 'value'=>''));
        return '<div class="simpleForm">
                    <p>'.__('passwordTempMessage').'</p>
                    '.Form::createForm($fields, array('action'=>url('User/updatePassword', true), 'class'=>'formAdmin', 'submit'=>__('send'))).'
                </div>';
    }

    public function changePassword() {
        $this->errors['oldPassword'] = isset($this->errors['oldPassword']) ? $this->errors['oldPassword'] : '';
        $fields = FormField_Password::create(array('label'=>__('oldPassword'), 'name'=>'oldPassword', 'error'=>$this->errors['oldPassword'])).'
                '.FormField_Password::create(array('label'=>__('password'), 'name'=>'password', 'error'=>$this->errors['oldPassword'], 'value'=>''));
        return '<div class="simpleForm">
                    <p>'.__('changePasswordMessage').'</p>
                    '.Form::createForm($fields, array('action'=>url('User/myAccountPassword', true), 'class'=>'formAdmin', 'submit'=>__('save'))).'
                </div>';
    }

    public function isValidChangePassword($user) {
        $errors = array();
        if (!isset($this->values['oldPassword']) || trim($this->values['oldPassword'])=='') {
            $errors['oldPassword'] = __('oldPasswordError');
        } else {        
            if ($user->get('passwordTemp')!='' && $this->values['oldPassword']!=$user->get('passwordTemp')) {
                $errors['oldPassword'] = __('oldPasswordError');
            } else {
                if (md5($this->values['oldPassword'])!=$user->get('password')) {
                    $errors['oldPassword'] = __('oldPasswordError');
                }
            }
        }
        $errors = array_merge($errors, $this->isValidField($this->object->attributeInfo('password')));
        return $errors;
    }

    public function myAccount() {
        $fields = $this->field('image').'
                '.$this->field('email').'
                '.$this->field('name').'
                '.$this->field('telephone').'
                '.$this->field('address');
        return '<div class="simpleForm">
                    <p>'.__('myAccountMessage').'</p>
                    '.Form::createForm($fields, array('action'=>url('User/myAccountPersonal', true), 'class'=>'formAdmin', 'submit'=>__('save'))).'
                </div>';
    }

    public function isValidMyInformation() {
        $errors = array();
        $items = array('email', 'name');
        foreach ($items as $item) {
            $error = $this->isValidField($this->object->attributeInfo($item));
            if (count($error)>0) {
                $errors = array_merge($error, $errors);
            }            
        }
        return $errors;
    }
    
}
?>