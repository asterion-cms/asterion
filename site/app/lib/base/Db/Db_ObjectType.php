<?php
/**
 * @class DbObjectType
 *
 * This class manages all the available types to use as attributes in the object.
 * The list includes:
 *       id_autoincrement
 *       id_char32
 *       id_varchar
 *       text
 *       text_code
 *       text_small
 *       text_large
 *       text_postalcode
 *       text_telephone
 *       text_integer
 *       text_double
 *       text_number
 *       text_email
 *       text_unchangeable
 *       hidden
 *       hidden_url
 *       hidden_login
 *       hidden_integer
 *       hidden_user_admin
 *       password
 *       textarea
 *       textarea-small
 *       textarea_large
 *       textarea_ck
 *       textarea_code
 *       select
 *       select_varchar
 *       select_link
 *       date
 *       date_complete
 *       date_hour
 *       date_text
 *       checkbox
 *       radio
 *       point
 *       file
 *       multiple_object
 *       multiple_checkbox
 *       multiple_autocomplete
 *       linkid_autoincrement
 *       linkid_char32
 *       linkid_varchar
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Db_ObjectType
{

    /**
     * Function to create the table based on the information in the XML file
     */
    public static function createAttributeSql($attribute)
    {
        $sql = '';
        if ((string) $attribute->language == 'true') {
            foreach (Language::languages() as $languageCode => $language) {
                $query = Db_ObjectType::createAttributeSqlSimple($attribute, $languageCode = '');
                $sql .= ($query != '') ? $query . ',' : '';
            }
        } else {
            $query = Db_ObjectType::createAttributeSqlSimple($attribute);
            $sql .= ($query != '') ? $query . ',' : '';
        }
        return $sql;
    }

    /**
     * Function to create the field based on the information in the XML file
     */
    public static function createAttributeSqlSimple($attribute, $language = '')
    {
        $name = (string) $attribute->name;
        $type = (string) $attribute->type;
        switch (Db_ObjectType::baseType($type)) {
            default:
                if ($language != '') {
                    return '`' . $name . '_' . $language . '` VARCHAR(255) COLLATE utf8_unicode_ci';
                } else {
                    return '`' . $name . '` VARCHAR(255) COLLATE utf8_unicode_ci';
                }
                break;
            case 'id':
                switch ($type) {
                    default:
                        return '`' . $name . '` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`'.$name.'`)';
                        break;
                    case 'id_char32':
                        return '`' . $name . '` CHAR(32) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY (`'.$name.'`)';
                        break;
                    case 'id_varchar':
                        return '`' . $name . '` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY (`'.$name.'`)';
                        break;
                }
                $sql .= 'PRIMARY KEY (`' . $name . '`),';
                break;
            case 'linkid':
                switch ($type) {
                    default:
                        return '`' . $name . '` INT NULL';
                        break;
                    case 'linkid_char32':
                        return '`' . $name . '` CHAR(32) NULL COLLATE utf8_unicode_ci';
                        break;
                    case 'linkid_varchar':
                        return '`' . $name . '` VARCHAR(255) NULL COLLATE utf8_unicode_ci';
                        break;
                }
                break;
            case 'textarea':
                if ($language != '') {
                    return '`' . $name . '_' . $language . '` TEXT COLLATE utf8_unicode_ci';
                } else {
                    return '`' . $name . '` TEXT COLLATE utf8_unicode_ci';
                }
                break;
            case 'checkbox':
            case 'radio':
                return '`' . $name . '` INT';
                break;
            case 'select':
                return '`' . $name . '` VARCHAR(255) NULL COLLATE utf8_unicode_ci';
                break;
            case 'date':
                return '`' . $name . '` DATETIME';
                break;
            case 'point':
                return '`' . $name . '` POINT NULL';
                break;
            case 'multiple':
                break;
        }
    }

    /**
     * Helper function to get the base type of an attribute
     */
    public static function baseType($type)
    {
        if (strpos($type, '_') !== false) {
            $typeInfo = explode('_', $type);
            return $typeInfo[0];
        }
        return $type;
    }

}
