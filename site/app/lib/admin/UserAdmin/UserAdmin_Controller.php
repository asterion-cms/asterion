<?php
/**
 * @class UserAdminController
 *
 * This class is the controller for the UserAdmin objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class UserAdmin_Controller extends Controller
{

    public function getContent()
    {
        $this->ui = new NavigationAdmin_Ui($this);
        switch ($this->action) {
            case 'login':
                $this->mode = 'admin';
                $this->layout_page = 'simple';
                $this->title_page = __('login');
                $login = UserAdmin_Login::getInstance();
                if (count($this->values) > 0) {
                    $login->checklogin($this->values);
                    if ($login->isConnected()) {
                        $newUrl = (hash('sha256', $this->values['password']) == hash('sha256', 'asterion')) ? 'user_admin/update_default_password' : '';
                        header('Location: ' . url($newUrl, true));
                        exit();
                    } else {
                        $form = new UserAdmin_Form();
                        $this->message_error = __('error_connection');
                        $this->content = $form->loginAdmin();
                    }
                } else {
                    if ($login->isConnected()) {
                        header('Location: ' . url('', true));
                        exit();
                    } else {
                        $form = new UserAdmin_Form();
                        $this->content = $form->loginAdmin();
                    }
                }
                return $this->ui->render();
                break;
            case 'update_default_password':
                $this->checkLoginAdmin();
                $this->mode = 'admin';
                $this->layout_page = 'simple';
                $this->title_page = __('update_default_password');
                $form = new UserAdmin_Form();
                if (count($this->values) > 0) {
                    $form = new UserAdmin_Form($this->values);
                    $errors = $form->isValidChangePasswordDefault();
                    if (count($errors) > 0) {
                        $form = new UserAdmin_Form($this->values, $errors);
                        $this->content = $form->changeDefaultPassword();
                    } else {
                        $this->login->userAdmin()->modifySimple('password', hash('sha256', $this->values['password']));
                        header('Location: '.url('', true));
                        exit();
                    }
                } else {
                    $this->content = $form->changeDefaultPassword();
                }
                return $this->ui->render();
                break;
            case 'logout':
                $this->checkLoginAdmin();
                $this->login->logout();
                header('Location: ' . url('', true));
                break;
            case 'forgot':
                $this->checkLoginAdmin();
                $this->mode = 'admin';
                $this->layout_page = 'simple';
                $this->title_page = __('password_forgot');
                $form = new UserAdmin_Form();
                if (isset($this->values['email'])) {
                    $userAdmin = (new UserAdmin)->readFirst(['where' => 'email="' . $this->values['email'] . '" AND active="1"']);
                    if ($userAdmin->id() != '') {
                        $tempPassword = substr(md5(rand() * rand()), 0, 10);
                        $userAdmin->modifySimple('temporary_password', $tempPassword);
                        $updatePasswordLink = url('user_admin/login', true);
                        HtmlMail::send($userAdmin->get('email'), 'password_forgot', ['TEMP_PASSWORD' => $tempPassword, 'UPDATE_PASSWORD_LINK' => $updatePasswordLink]);
                        $this->content = $form->forgotSent();
                    } else {
                        $this->message_error = __('mail-doesnt-exist');
                        $this->content = $form->forgot();
                    }
                } else {
                    $form = new UserAdmin_Form();
                    $this->content = $form->forgot();
                }
                return $this->ui->render();
                break;
            case 'first_password':
                $this->checkLoginAdmin();
                $this->mode = 'admin';
                $this->layout_page = 'clean';
                $this->title_page = __('first_password');
                $form = UserAdmin_Form::fromObject($this->login->userAdmin());
                $this->content = $form->changePassword();
                return $this->ui->render();
                break;
            case 'account':
                $this->checkLoginAdmin();
                $this->mode = 'admin';
                $this->title_page = __('account');
                $form = UserAdmin_Form::fromObject($this->login->userAdmin());
                $this->message = ($this->id == 'success_personal') ? __('saved_form') : '';
                $this->message = ($this->id == 'success_password') ? __('change_password_success') : $this->message;
                $this->message_error = ($this->id == 'error_personal') ? __('errors_form') : '';
                $this->message_error = ($this->id == 'error_password') ? __('change_password_error') : $this->message_error;
                $this->content = '<div class="simple_grid simple_grid_spacing_20">
                                        <div class="simple_grid_item_6">' . $form->account() . '</div>
                                        <div class="simple_grid_item_6">' . $form->changePassword() . '</div>
                                    </div>';
                return $this->ui->render();
                break;
            case 'account_personal':
                $this->checkLoginAdmin();
                if (count($this->values) > 0) {
                    $this->values['id'] = $this->login->id();
                    $form = new UserAdmin_Form($this->values);
                    $errors = $form->isValidPersonal($this->values);
                    if (count($errors) > 0) {
                        header('Location: ' . url('user_admin/account/error_personal', true));
                    } else {
                        $this->login->userAdmin()->modify($this->values, ['complete' => false]);
                        header('Location: ' . url('user_admin/account/success_personal', true));
                    }
                    exit();
                }
                header('Location: ' . url('user_admin/account', true));
                exit();
                break;
            case 'account_password':
                $this->checkLoginAdmin();
                if (count($this->values) > 0) {
                    $this->values['id'] = $this->login->id();
                    $form = new UserAdmin_Form($this->values);
                    $errors = $form->isValidChangePassword($this->login->userAdmin());
                    if (count($errors) > 0) {
                        header('Location: ' . url('user_admin/account/error_password', true));
                    } else {
                        $this->login->userAdmin()->modify($this->values, ['complete' => false]);
                        $this->login->userAdmin()->modifySimple('temporary_password', '');
                        header('Location: ' . url('user_admin/account/success_password', true));
                    }
                    exit();
                }
                header('Location: ' . url('user_admin/account', true));
                exit();
                break;
        }
        return parent::getContent();
    }
}
