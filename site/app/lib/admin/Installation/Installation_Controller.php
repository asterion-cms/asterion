<?php
/**
 * @class InstallationController
 *
 * This is the controller for all the actions in the administration area.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Installation_Controller extends Controller
{

    /**
     * Main function to control the administration system.
     */
    public function getContent()
    {
        $ui = new NavigationAdmin_Ui($this);
        $this->mode = 'admin';
        $this->layout_page = 'clear';
        if (!ASTERION_DEBUG) {
            header('Location: ' . url(''));
            exit();
        }
        switch ($this->action) {
            default:
                $this->title_page = 'Configuration';
                $this->content = Installation_Ui::renderDatabaseConnection();
                return $ui->render();
                break;
            case 'database':
                $this->title_page = 'Database';
                $this->content = Installation_Ui::renderDatabase();
                return $ui->render();
                break;
            case 'update_database':
                foreach (Init::errorsDatabase() as $item) {
                    Db::execute($item['query']);
                }
                header('Location: ' . url('', true));
                exit();
                break;
            case 'languages':
                $this->layout_page = 'clear';
                $this->title_page = 'Languages';
                if (count($this->values) > 0) {
                    Session::set('languages_creation', array_keys($this->values['language']));
                    $this->content = Installation_Ui::renderLanguagesVerification(array_keys($this->values['language']));
                    return $ui->render();
                }
                $this->content = Installation_Ui::renderLanguages();
                return $ui->render();
                break;
            case 'install_languages':
                $languages = Session::get('languages_creation');
                if (count($languages) > 0) {
                    $isoLanguages = Language::isoList();
                    foreach ($languages as $language) {
                        if (isset($isoLanguages[$language])) {
                            $newLanguage = (new Language)->read($language);
                            if ($newLanguage->id() == '') {
                                $newLanguage->insert(['id' => $language, 'name' => $isoLanguages[$language]['name'], 'local_names' => $isoLanguages[$language]['local_names']]);
                            }
                        }
                        header('Location: ' . url('', true));
                    }
                } else {
                    header('Location: ' . url('installation/languages', true));
                }
                exit();
                break;
        }
    }

}
