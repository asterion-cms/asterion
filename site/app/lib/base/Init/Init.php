<?php
/**
 * @class Init
 *
 * This class contains static functions to initialize the website.
 * It is only called in ASTERION_DEBUG mode and it helps to setup Asterion for the first time.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Init
{

    /**
     * Asterion initializes the common services.
     * It parses the URL and then checks if the database is correct.
     * It also loads the translations and parameters.
     */
    public static function initSite()
    {
        Url::init();
        if (ASTERION_DEBUG) {
            if (ASTERION_DB_USE) {
                if ($_GET['type'] == 'installation') {
                    return true;
                }
                if (!Db_Connection::testConnection()) {
                    header('Location: ' . url('installation', true));
                    exit();
                }
                $errorsDatabase = Init::errorsDatabase();
                if (count($errorsDatabase) > 0) {
                    header('Location: ' . url('installation/database', true));
                    exit();
                }
                if (count(Language::languages()) == 0) {
                    header('Location: ' . url('installation/languages', true));
                    exit();
                }
                Params::saveInitialValues();
                $objectNames = File::scanDirectoryObjects();
                foreach ($objectNames as $objectName) {
                    $options = ($objectName == 'UserAdmin') ? ['EMAIL' => ASTERION_EMAIL] : [];
                    Init::saveInitialValues($objectName, $options);
                }
            }
        }
        Language::init();
        Params::init();
    }

    /**
     * Check if the database is correct.
     */
    public static function errorsDatabase()
    {
        $errors = [];
        $objectNames = File::scanDirectoryObjects();
        foreach ($objectNames as $objectName) {
            $object = new $objectName;
            if (!Db::tableExists($object->tableName)) {
                $errors[] = ['object' => $objectName, 'action' => 'create', 'query' => $object->createTableQuery()];
            } else {
                $tableDescription = Db::describe($object->tableName);
                foreach ($object->info->attributes->attribute as $attribute) {
                    $name = (string) $attribute->name;
                    $type = (string) $attribute->type;
                    if (Db_ObjectType::baseType($type) != 'multiple') {
                        if ((string) $attribute->language == 'true') {
                            foreach (Language::languages() as $languageCode => $language) {
                                if (!isset($tableDescription[$name . '_' . $languageCode])) {
                                    $errors[] = ['object' => $objectName, 'field' => $name, 'action' => 'update', 'query' => $object->updateAttributeQuery($attribute, $languageCode)];
                                }
                            }
                        } else {
                            if (!isset($tableDescription[$name])) {
                                $errors[] = ['object' => $objectName, 'field' => $name, 'action' => 'update', 'query' => $object->updateAttributeQuery($attribute)];
                            }
                        }
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Load the initial values at the time of installation
     * and save them in the database.
     */
    public static function saveInitialValues($className, $extraValues = [])
    {
        $object = new $className;
        $object->createTable();
        $numberItems = $object->countResults();
        $dataUrl = ASTERION_DATA_FILE . $className . '.json';
        if (file_exists($dataUrl) && $numberItems == 0) {
            $items = json_decode(file_get_contents($dataUrl), true);
            foreach ($items as $item) {
                $itemSave = new $className;
                if (count($extraValues) > 0) {
                    foreach ($extraValues as $keyExtraValue => $itemExtraValue) {
                        foreach ($item as $keyItem => $eleItem) {
                            $item[$keyItem] = str_replace('##' . $keyExtraValue, $itemExtraValue, $eleItem);
                        }
                    }
                }
                $itemSave->insert($item);
            }
        }
    }

    /**
     * Save the Translation items for a new language.
     */
    public static function saveTranslation($lang)
    {
        $className = 'Translation';
        $object = new $className;
        $object->createTable();
        $dataUrl = ASTERION_DATA_FILE . $className . '.json';
        if (file_exists($dataUrl)) {
            $items = json_decode(file_get_contents($dataUrl), true);
            $itemTranslation = 'translation_' . $lang;
            foreach ($items as $item) {
                if (isset($item[$itemTranslation])) {
                    $query = 'UPDATE ' . Db::prefixTable('translation') . '
                                SET ' . $itemTranslation . '="' . $item[$itemTranslation] . '"
                                WHERE code="' . $item['code'] . '"';
                    Db::execute($query);
                }
            }
        }
    }

}
