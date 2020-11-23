<?php
/**
 * @class UserAdminForm
 *
 * This class manages the forms for the UserAdmin objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class UserAdmin_Form extends Form
{

    public function loginAdmin()
    {
        $fields = $this->field('email') . '
                ' . $this->field('password');
        return '<div class="simple_form">
                    <p>' . __('login_message') . '</p>
                    ' . Form::createForm($fields, ['action' => url('user_admin/login', true), 'class' => 'form_admin', 'submit' => __('send')]) . '
                    <div class="simple_form_actions">
                        <a href="' . url('user_admin/forgot', true) . '">' . __('password_forgot') . '</a>
                    </div>
                </div>';
    }

    public function forgot()
    {
        $fields = $this->field('email');
        return '<div class="simple_form">
                    <p>' . __('password_forgot_message') . '</p>
                    ' . Form::createForm($fields, ['action' => url('user_admin/forgot', true), 'class' => 'form_admin', 'submit' => __('send')]) . '
                    <div class="simple_form_actions">
                        <a href="' . url('user_admin/login', true) . '">' . __('try_login_again') . '</a>
                    </div>
                </div>';
    }

    public function forgotSent()
    {
        return '<div class="simple_form">
                    <div class="message">' . __('password_sent_mail') . '</div>
                    <div class="simple_form_actions">
                        <a href="' . url('user_admin/login', true) . '">' . __('try_login_again') . '</a>
                    </div>
                </div>';
    }

    public function updatePassword()
    {
        $fields = $this->field('email') . '
                ' . FormField_Password::create(['label' => __('password'), 'name' => 'password', 'error' => $this->errors['old_password'], 'value' => '']) . '
                ' . FormField_Password::create(['label' => __('password_confirmation'), 'name' => 'password_confirmation', 'error' => $this->errors['password_confirmation'], 'value' => '']);
        return '<div class="simple_form">
                    <p>' . __('temporary_password_message') . '</p>
                    ' . Form::createForm($fields, ['action' => url('user_admin/update_password', true), 'class' => 'form_admin', 'submit' => __('send')]) . '
                </div>';
    }

    public function changePassword()
    {
        $this->errors['old_password'] = isset($this->errors['old_password']) ? $this->errors['old_password'] : '';
        $fields = FormField_Password::create(['label' => __('old_password'), 'name' => 'old_password', 'error' => $this->errors['old_password'], 'value' => '']) . '
                ' . FormField_Password::create(['label' => __('password'), 'name' => 'password', 'error' => $this->errors['old_password'], 'value' => '']);
        return '<div class="simple_form">
                    <h2>' . __('update_password') . '</h2>
                    <p>' . __('change_password_message') . '</p>
                    ' . Form::createForm($fields, ['action' => url('user_admin/account_password', true), 'class' => 'form_admin', 'submit' => __('save')]) . '
                </div>';
    }

    public function changeDefaultPassword()
    {
        $this->errors['password'] = isset($this->errors['password']) ? $this->errors['password'] : '';
        $this->errors['password_confirmation'] = isset($this->errors['password_confirmation']) ? $this->errors['password_confirmation'] : '';
        $fields = FormField_Password::create(['label' => __('password'), 'name' => 'password', 'error' => $this->errors['password'], 'value' => '']).'
            '.FormField_Password::create(['label' => __('password_confirmation'), 'name' => 'password_confirmation', 'error' => $this->errors['password_confirmation'], 'value' => '']);
        return '<div class="simple_form">
                    <p>' . __('update_default_password_message') . '</p>
                    ' . Form::createForm($fields, ['action' => url('user_admin/update_default_password', true), 'class' => 'form_admin', 'submit' => __('save')]) . '
                </div>';
    }

    public function isValidChangePassword($userAdmin)
    {
        $errors = [];
        if (!isset($this->values['old_password']) || trim($this->values['old_password']) == '') {
            $errors['old_password'] = __('old_password_error');
        } else {
            if ($userAdmin->get('temporary_password') != '' && $this->values['old_password'] != $userAdmin->get('temporary_password')) {
                $errors['old_password'] = __('old_password_error');
            } else {
                if (hash('sha256', $this->values['old_password']) != $userAdmin->get('password')) {
                    $errors['old_password'] = __('old_password_error');
                }
            }
        }
        $errors = array_merge($errors, $this->isValidField($this->object->attributeInfo('password')));
        return $errors;
    }

    public function isValidChangePasswordDefault()
    {
        $errors = [];
        if (!isset($this->values['password']) || trim($this->values['password']) == '') {
            $errors['password'] = __('not_empty');
        } else if (!isset($this->values['password_confirmation']) || trim($this->values['password_confirmation']) == '') {
            $errors['password_confirmation'] = __('not_empty');
        } else {
            $errorPassword = Form::validatePassword($this->values['password']);
            if ($errorPassword!='') {
                $errors['password'] = $errorPassword;
            }
            if ($this->values['password'] != $this->values['password_confirmation']) {
                $errors['password_confirmation'] = __('password_confirmation_error');
            }
        }
        return $errors;
    }

    public function account()
    {
        $fields = $this->field('image') . '
                ' . $this->field('email') . '
                ' . $this->field('name') . '
                ' . $this->field('telephone');
        return '<div class="simple_form">
                    <h2>' . __('personal_information') . '</h2>
                    <p>' . __('account_message') . '</p>
                    ' . Form::createForm($fields, ['action' => url('user_admin/account_personal', true), 'class' => 'form_admin', 'submit' => __('save')]) . '
                </div>';
    }

    public function isValidPersonal()
    {
        $errors = [];
        $items = ['email', 'name'];
        foreach ($items as $item) {
            $error = $this->isValidField($this->object->attributeInfo($item));
            if (count($error) > 0) {
                $errors = array_merge($error, $errors);
            }
        }
        return $errors;
    }

}
