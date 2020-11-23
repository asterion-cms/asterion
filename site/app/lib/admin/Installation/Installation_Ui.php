<?php
/**
 * @class HtmlSectionAdminUi
 *
 * This class manages the UI for the HtmlSectionAdmin objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Installation_Ui extends Ui
{

    /**
     * Render the actual configuration.
     */
    public static function renderConfiguration()
    {
        $groups = [
            'main' => [
                'title' => 'Main',
                'items' => ['ASTERION_TITLE', 'ASTERION_DEBUG', 'ASTERION_EMAIL'],
            ],
            'db' => [
                'title' => 'Database',
                'message' => 'The configuration for the database',
                'items' => ['ASTERION_DB_USE', 'ASTERION_DB_SERVER', 'ASTERION_DB_USER', 'ASTERION_DB_PASSWORD', 'ASTERION_DB_PORT', 'ASTERION_DB_NAME', 'ASTERION_DB_PREFIX'],
            ],
            'folders' => [
                'title' => 'Folders',
                'message' => 'These are the locations that Asterion will use',
                'items' => ['ASTERION_SERVER_URL', 'ASTERION_BASE_STRING', 'ASTERION_LOCAL_URL', 'ASTERION_LOCAL_FILE', 'ASTERION_BASE_URL', 'ASTERION_BASE_FILE', 'ASTERION_APP_URL', 'ASTERION_APP_FILE', 'ASTERION_MODEL_FILE', 'ASTERION_FRAMEWORK_FILE', 'ASTERION_ADMIN_URL', 'ASTERION_STOCK_URL', 'ASTERION_STOCK_FILE', 'ASTERION_DATA_FILE'],
                'session' => ['ASTERION_SESSION_NAME', 'ASTERION_COOKIE_TIME'],
            ],
            'url' => [
                'title' => 'URL',
                'message' => 'Here are the URL strings for the system',
                'items' => ['ASTERION_ADMIN_URL_STRING', 'ASTERION_PAGER_URL_STRING'],
            ],
            'images' => [
                'title' => 'Images',
                'message' => 'Configuration for managing the images in the system',
                'items' => ['ASTERION_LOGO', 'ASTERION_SAVE_IMAGE_ORIGINAL', 'ASTERION_SAVE_IMAGE_ORIGINAL', 'ASTERION_SAVE_IMAGE_HUGE', 'ASTERION_SAVE_IMAGE_WEB', 'ASTERION_SAVE_IMAGE_SMALL', 'ASTERION_SAVE_IMAGE_THUMB', 'ASTERION_SAVE_IMAGE_SQUARE', 'ASTERION_WIDTH_HUGE', 'ASTERION_HEIGHT_MAX_HUGE', 'ASTERION_WIDTH_WEB', 'ASTERION_HEIGHT_MAX_WEB', 'ASTERION_WIDTH_SMALL', 'ASTERION_HEIGHT_MAX_SMALL', 'ASTERION_WIDTH_THUMB', 'ASTERION_HEIGHT_MAX_THUMB', 'ASTERION_WIDTH_SQUARE'],
            ]];
        $content = '';
        foreach ($groups as $code => $group) {
            $contentGroup = '';
            foreach ($group['items'] as $item) {
                $contentGroup .= '<div class="simple_grid_item_6"><strong>' . $item . '</strong></div>
                                <div class="simple_grid_item_6"><span>' . constant($item) . '</span></div>';
            }
            $content .= '<div class="configuration_group">
                            <h2>' . $group['title'] . '</h2>
                            ' . (isset($group['message']) ? '<p>' . $group['message'] . '</p>' : '') . '
                            <div class="configuration_group_items">
                                <div class="simple_grid simple_grid_border">' . $contentGroup . '</div>
                            </div>
                        </div>';
        }
        return '<div class="configuration">
                    <p>Here are the configuration parameters, you should find the in the <strong>config.php</strong> files. Please check them before proceding to the installation.</p>
                    ' . $content . '
                </div>';

    }

    public static function renderDatabaseConnection()
    {
        $errorConnection = (!Db_Connection::testConnection()) ? '<div class="message message_error">We cannot connect to the database</div>' : '';
        $items = ['ASTERION_DB_SERVER', 'ASTERION_DB_USER', 'ASTERION_DB_PASSWORD', 'ASTERION_DB_PORT', 'ASTERION_DB_NAME', 'ASTERION_DB_PREFIX'];
        $content = '';
        foreach ($items as $item) {
            $content .= '<div class="simple_grid_item_6"><strong>' . $item . '</strong></div>
                        <div class="simple_grid_item_6"><span>' . constant($item) . '</span></div>';
        }
        return '<div class="configuration">
                    ' . $errorConnection . '
                    <p>Here are the configuration parameters, you should find the in the <strong>config.php</strong> files. Please check them and try again.</p>
                    <div class="configuration_group_items">
                        <div class="simple_grid simple_grid_border">' . $content . '</div>
                    </div>
                </div>';
    }

    /**
     * Render the database actions.
     */
    public static function renderDatabase()
    {
        $errors = Init::errorsDatabase();
        if (count($errors) > 0) {
            $content = '';
            $queries = '';
            foreach ($errors as $error) {
                $label = '';
                if ($error['action'] == 'create') {
                    $label = 'Create the table for the object <strong>' . $error['object'] . '</strong>';
                }
                if ($error['action'] == 'update') {
                    $label = 'Update the attribute <strong>' . $error['field'] . '</strong> for the object <strong>' . $error['object'] . '</strong>';
                }
                $content .= '<p>' . $label . '</p>';
                $queries .= '<pre>' . $error['query'] . '</pre>';
            }
            return '<div class="configuration">
                        <div class="message message_error">
                            <p>The database model is not updated with the information of your website.</p>
                            <p>Please take caution because you might loose information, so backup all your information.</p>
                        </div>
                        <div class="buttons_center">
                            <a href="' . url('installation/update_database', true) . '" class="button">Update database</a>
                        </div>
                        <p>Here are the actions that the system will perform:</p>
                        <div class="list">' . $content . '</div>
                        <p>You can also run them manually, the queries are:</p>
                        <div class="code">' . $queries . '</div>
                    </div>';
        } else {
            return '<div class="configuration">
                        <div class="message">
                            <p>The database model is updated with the information of your website.</p>
                        </div>
                    </div>';
        }
    }

    /**
     * Render the language actions.
     */
    public static function renderLanguages()
    {
        $languages = Language::languages();
        if (count($languages) > 0) {
            return '<div class="configuration">
                        <div class="message">
                            <p>The have installed languages for your website, please use the administrator to update them.</p>
                        </div>
                    </div>';
        } else {
            return '<div class="configuration">
                        <div class="message message_error">
                            <p>You need to define at least one language for your website.</p>
                        </div>
                        <p>If your website has one language the best option is to use the <strong>ASTERION_LANGUAGE_ID</strong> constant in the <strong>config.php</strong> file.</p>
                        <p>If your website has multiple languages please choose them :</p>
                        ' . Language_Form::createFormIso() . '
                    </div>';
        }

    }

    /**
     * Render the language verifications.
     */
    public static function renderLanguagesVerification($languageCodes)
    {
        $content = '';
        $languages = Language::isoList();
        foreach ($languageCodes as $languageCode) {
            $language = $languages[$languageCode];
            $content .= '<div class="iso_language">
                            <p><strong>' . $languageCode . '</strong> ' . $language['name'] . '</p>
                            <p><span>' . $language['local_names'] . '</span></p>
                        </div>';
        }
        $fields = FormField_Hidden::create(['name' => 'langs_verified', 'value' => true]);
        return '<div class="configuration">
                    <p>Please confirm the installation of the following languages.</p>
                    <div class="form_admin_simple">
                        <div class="iso_languages">
                            ' . $content . '
                        </div>
                    </div>
                    <div class="buttons_center">
                        <a href="' . url('installation/languages', true) . '" class="button button_cancel">Cancel</a>
                        <a href="' . url('installation/install_languages', true) . '" class="button">Install</a>
                    </div>
                </div>';
    }

}
