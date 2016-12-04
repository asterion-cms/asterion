<?php
/**
* @class DbObjectType
*
* This class manages all the available types to use as attributes in the object.
* The list includes:
*       id-autoincrement
*       id-char32
*       id-varchar
*       text
*       text-code
*       text-small
*       text-large
*       text-postalcode
*       text-telephone
*       text-integer
*       text-double
*       text-number
*       text-email
*       text-unchangeable
*       hidden
*       hidden-url
*       hidden-login
*       hidden-integer
*       hidden-user
*       password
*       textarea
*       textarea-small
*       textarea-large
*       textarea-ck
*       textarea-code
*       select
*       select-varchar
*       select-link
*       date
*       date-complete
*       date-hour
*       date-text
*       checkbox
*       radio
*       point
*       file
*       multiple-object
*       multiple-checkbox
*       multiple-autocomplete
*       linkid-autoincrement
*       linkid-char32
*       linkid-varchar
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Db_ObjectType {

    /**
    * Function to create the table based on the information in the XML file
    */
    static public function createTableSql($item) {
        $sql = '';
        $name = (string)$item->name;
        $type = (string)$item->type;
        switch (Db_ObjectType::baseType($type)) {
            default:
                if ((string)$item->lang == 'true') {
                    foreach (Lang::langs() as $lang) {
                        $sql .= '`'.$name.'_'.$lang.'` VARCHAR(255) COLLATE utf8_unicode_ci,';
                    }
                } else {
                    $sql .= '`'.$name.'` VARCHAR(255) COLLATE utf8_unicode_ci,';
                }
            break;
            case 'id':
                switch ($type) {
                    default:
                        $sql .= '`'.$name.'` INT NOT NULL AUTO_INCREMENT,';
                    break;
                    case 'id-char32':
                        $sql .= '`'.$name.'` CHAR(32) NOT NULL COLLATE utf8_unicode_ci,';
                    break;
                    case 'id-varchar':
                        $sql .= '`'.$name.'` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci,';
                    break;
                }
                $sql .= 'PRIMARY KEY (`'.$name.'`),';
            break;
            case 'linkid':
                switch ($type) {
                    default:
                        $sql .= '`'.$name.'` INT NULL,';
                    break;
                    case 'linkid-char32':
                        $sql .= '`'.$name.'` CHAR(32) NULL COLLATE utf8_unicode_ci,';
                    break;
                    case 'linkid-varchar':
                        $sql .= '`'.$name.'` VARCHAR(255) NULL COLLATE utf8_unicode_ci,';
                    break;
                }
            break;
            case 'textarea':
                if ((string)$item->lang == 'true') {
                    foreach (Lang::langs() as $lang) {
                        $sql .= '`'.$name.'_'.$lang.'` TEXT COLLATE utf8_unicode_ci,';
                    }
                } else {
                    $sql .= '`'.$name.'` TEXT COLLATE utf8_unicode_ci,';
                }
            break;
            case 'checkbox':
            case 'radio':
                $sql .= '`'.$name.'` INT,';
            break;
            case 'select':
                switch ($type) {
                    default:
                        $sql .= '`'.$name.'` INT,';
                    break;
                    case 'select-varchar':
                    case 'select-link':
                        $sql .= '`'.$name.'` VARCHAR(255) NULL COLLATE utf8_unicode_ci,';
                    break;
                }
            break;
            case 'date':
                $sql .= '`'.$name.'` DATETIME,';
            break;
            case 'point':
                $sql .= '`'.$name.'` POINT NULL,';
            break;
            case 'multiple':
            break;
        }
        return $sql;
    }

    /**
    * Helper function to get the base type of an attribute
    */
    static public function baseType($type) {
        if (strpos($type, '-')!==false) {
            $typeInfo = explode('-', $type);
            return $typeInfo[0];
        }
        return $type;
    }

}
?>