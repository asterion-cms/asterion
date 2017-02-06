<?php
/**
* @class NavigationAdminController
*
* This is the controller for all the actions in the administration area.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class NavigationAdmin_Controller extends Controller{

    /**
    * Main function to control the administration system.
    */
    public function controlActions(){
        $ui = new NavigationAdmin_Ui($this);
        $this->mode = 'admin';
        switch ($this->action) {
            default:
                $this->login = User::loginAdmin();
                if ($this->login->user()->get('passwordTemp')!='') {
                    $linkMyInformation = '<a href="'.url('User/myAccount', true).'">'.__('here').'</a>';
                    $this->messageAlert = str_replace('#HERE', $linkMyInformation, __('changeYourTemporaryPassword'));
                }
                $this->content = '<div class="pageIntro">'.HtmlSectionAdmin::show('intro').'</div>';
                return $ui->render();
            break;
            case 'permissions':
                $this->titlePage = TITLE;
                $this->messageError = __('permissionsError');
                return $ui->render();
            break;
            case 'base-info':
                $this->mode = 'js';
                $info = array(
                            'base_url'=>LOCAL_URL,
                            'base_file'=>LOCAL_FILE,
                            'app_url'=>APP_URL,
                            'app_folder'=>APP_FOLDER,
                            'site_url'=>url(''),
                            'lang'=>Lang::active()
                            );
                return 'var info_site = '.json_encode($info).';';
            break;
            case 'js-translations':
                $this->mode = 'js';
                $query = 'SELECT code, translation_'.Lang::active().' as translation
                        FROM '.Db::prefixTable('LangTrans').'
                        WHERE code LIKE "js_%"';
                $translations = array();
                $translationResults = Db::returnAll($query);
                foreach ($translationResults as $translationResult) {
                    $trasnlationValue = $translationResult['translation'];
                    $translations[$translationResult['code']] = $trasnlationValue;
                }
                return 'var info_translations = '.json_encode($translations).';';
            break;
            case 'backup':
                $this->checkLoginAdmin();
                $this->titlePage = __('backup');
                File::createDirectory(BASE_FILE.'data/backup');
                if (!is_writable(BASE_FILE.'data/backup')) {
                    $this->messageError = str_replace('#DIRECTORY', BASE_FILE.'data/backup', __('directoryNotWritable'));
                } else {
                    $this->content = Backup::intro();
                }
                return $ui->render();
            break;
            case 'reset-object':
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                Db::execute('DROP TABLE IF EXISTS `'.Db::prefixTable($this->id).'`');
                header('Location: '.url('NavigationAdmin/backup', true));
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
                $this->titlePage = __('cache');
                File::createDirectory(BASE_FILE.'cache', false);
                if (!is_writable(BASE_FILE.'cache')) {
                    $this->messageError = str_replace('#DIRECTORY', BASE_FILE.'cache', __('directoryNotWritable'));
                } else {
                    $this->message = ($this->id=='cached') ? __('objectCached') : '';
                    $this->message = ($this->id=='cachedAll') ? __('objectsCached') : $this->message;
                    $this->content = Cache::intro();
                }
                return $ui->render();
            break;
            case 'cache-all':
                $this->checkLoginAdmin();
                File::createDirectory(BASE_FILE.'cache', false);
                if (!is_writable(BASE_FILE.'cache')) {
                    $this->titlePage = __('cache');
                    $this->messageError = str_replace('#DIRECTORY', BASE_FILE.'cache', __('directoryNotWritable'));
                } else {
                    Cache::cacheAll();
                    header('Location: '.url('NavigationAdmin/cache', true));
                    exit();
                }
                return $ui->render();
            break;
            case 'cache-object':
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if (class_exists($this->id.'_Ui')) {
                    File::createDirectory(BASE_FILE.'cache', false);
                    File::createDirectory(BASE_FILE.'cache/'.$this->id, false);
                    if (!is_writable(BASE_FILE.'cache/'.$this->id)) {
                        $this->titlePage = __('cache');
                        $this->messageError = str_replace('#DIRECTORY', BASE_FILE.'cache', __('directoryNotWritable'));
                    } else {
                        Cache::cacheObject($this->id);
                    }
                }
                header('Location: '.url('NavigationAdmin/cache/cached', true));
                exit();
            break;
        }
    }

}
?>