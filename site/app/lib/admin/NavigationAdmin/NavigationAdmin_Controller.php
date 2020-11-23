<?php
/**
 * @class NavigationAdminController
 *
 * This is the controller for all the actions in the administration area.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class NavigationAdmin_Controller extends Controller
{

    /**
     * Main function to control the administration system.
     */
    public function getContent()
    {
        $ui = new NavigationAdmin_Ui($this);
        $this->mode = 'admin';
        switch ($this->action) {
            default:
                $login = UserAdmin::loginAdmin();
                if ($login->userAdmin()->get('temporary_password') != '') {
                    $linkMyInformation = '<a href="' . url('user_admin/account', true) . '">' . __('here') . '</a>';
                    $this->message_alert = str_replace('#HERE', $linkMyInformation, __('change-your-temporary_password'));
                }
                $this->content = '<div class="page-intro">' . HtmlSectionAdmin::show('intro') . '</div>';
                return $ui->render();
                break;
            case 'permissions':
                $this->title_page = ASTERION_TITLE;
                $this->message_error = __('permissions-error');
                return $ui->render();
                break;
            case 'js':
                $this->mode = 'js';
                $info = [
                    'base_url' => ASTERION_LOCAL_URL,
                    'base_file' => ASTERION_LOCAL_FILE,
                    'app_url' => ASTERION_APP_URL,
                    'app_folder' => APP_FOLDER,
                    'site_url' => url(''),
                    'lang' => Language::active()
                ];
                return 'var info_site = ' . json_encode($info) . ';
                        var info_translations = ' . json_encode(Translation::load(Language::active())) . ';';
                break;
            case 'backup':
                $this->checkLoginAdmin();
                $this->title_page = __('backup');
                File::createDirectory(ASTERION_BASE_FILE . 'data/backup');
                if (!is_writable(ASTERION_BASE_FILE . 'data/backup')) {
                    $this->message_error = str_replace('#DIRECTORY', ASTERION_BASE_FILE . 'data/backup', __('directoryNotWritable'));
                } else {
                    $this->content = Backup::intro();
                }
                return $ui->render();
                break;
            case 'reset-object':
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                Db::execute('DROP TABLE IF EXISTS `' . Db::prefixTable($this->id) . '`');
                header('Location: ' . url('NavigationAdmin/backup', true));
                exit();
                break;
            case 'backup-sql':
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                Backup::backupSql($this->id);
                return '';
                break;
            case 'backup-json':
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if ($this->id != '') {
                    Backup::backupJson($this->id);
                } else {
                    $this->mode = 'zip';
                    Backup::backupJson();
                }
                return '';
                break;
            case 'cache':
                $this->checkLoginAdmin();
                $this->title_page = __('cache');
                File::createDirectory(ASTERION_BASE_FILE . 'cache', false);
                if (!is_writable(ASTERION_BASE_FILE . 'cache')) {
                    $this->message_error = str_replace('#DIRECTORY', ASTERION_BASE_FILE . 'cache', __('directoryNotWritable'));
                } else {
                    $this->message = ($this->id == 'cached') ? __('objectCached') : '';
                    $this->message = ($this->id == 'cachedAll') ? __('objectsCached') : $this->message;
                    $this->content = Cache::intro();
                }
                return $ui->render();
                break;
            case 'cache-all':
                $this->checkLoginAdmin();
                File::createDirectory(ASTERION_BASE_FILE . 'cache', false);
                if (!is_writable(ASTERION_BASE_FILE . 'cache')) {
                    $this->title_page = __('cache');
                    $this->message_error = str_replace('#DIRECTORY', ASTERION_BASE_FILE . 'cache', __('directoryNotWritable'));
                } else {
                    Cache::cacheAll();
                    header('Location: ' . url('NavigationAdmin/cache', true));
                    exit();
                }
                return $ui->render();
                break;
            case 'cache-object':
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if (class_exists($this->id . '_Ui')) {
                    File::createDirectory(ASTERION_BASE_FILE . 'cache', false);
                    File::createDirectory(ASTERION_BASE_FILE . 'cache/' . $this->id, false);
                    if (!is_writable(ASTERION_BASE_FILE . 'cache/' . $this->id)) {
                        $this->title_page = __('cache');
                        $this->message_error = str_replace('#DIRECTORY', ASTERION_BASE_FILE . 'cache', __('directoryNotWritable'));
                    } else {
                        Cache::cacheObject($this->id);
                    }
                }
                header('Location: ' . url('NavigationAdmin/cache/cached', true));
                exit();
                break;
        }
    }

}
