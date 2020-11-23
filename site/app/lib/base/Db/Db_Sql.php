<?php
/**
 * @class DbSql
 *
 * This is the class that connects the object with the database in a logical level.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Db_Sql
{

    /**
     * Construct the object.
     */
    public function __construct($values = [])
    {
        $this->className = get_class($this);
        $this->info = XML::readClass($this->className);
        $this->tableName = Db::prefixTable($this->info->table);
        $this->primary = (string) $this->info->info->sql->primary;
        $this->title = __((string) $this->info->info->form->title);
        $ord = (isset($values['ord'])) ? $values['ord'] : '';
        $created = (isset($values['created'])) ? $values['created'] : '';
        $modified = (isset($values['modified'])) ? $values['modified'] : '';
        $this->values = ['created' => $created, 'modified' => $modified, 'ord' => $ord];
    }

    /**
     * Counts the number of objects in the DB (static function).
     */
    public function countResults($options = [], $values = [])
    {
        $where = (isset($options['where']) && $options['where'] != '') ? $options['where'] : '1=1';
        $query = 'SELECT COUNT(*) AS count_items
                        FROM ' . $this->tableName . '
                        WHERE ' . $where;
        $result = Db::returnSingle($query, $values);
        return $result['count_items'];
    }

    /**
     * Returns the values of an object (static function).
     */
    public function readValues($id, $options = [])
    {
        $fields = (isset($options['fields'])) ? $options['fields'] : '*';
        $query = 'SELECT ' . $fields . $this->fieldPoints() . '
                    FROM ' . $this->tableName . '
                    WHERE ' . $this->primary . '=:id';
        return Db::returnSingle($query, ['id' => $id]);
    }

    /**
     * Returns a single object using its id (static function).
     */
    public function read($id, $options = [])
    {
        $values = $this->readValues($id, $options);
        return new $this->className($values);
    }

    /**
     * Returns a single object (static function).
     */
    public function readFirst($options = [], $values = [])
    {
        $values = (isset($options['values'])) ? $options['values'] : $values;
        $fields = (isset($options['fields'])) ? $options['fields'] : '*';
        $where = (isset($options['where']) && $options['where'] != '') ? $options['where'] : '1=1';
        $order = (isset($options['order']) && $options['order'] != '') ? ' ORDER BY ' . $options['order'] : '';
        $limit = (isset($options['limit']) && $options['limit'] != '') ? ' LIMIT ' . $options['limit'] . ',1' : ' LIMIT 1';
        $query = 'SELECT ' . $fields . $this->fieldPoints() . '
                    FROM ' . $this->tableName . '
                    WHERE ' . $where . '
                    ' . $order . '
                    ' . $limit;
        return new $this->className(Db::returnSingle($query, $values));
    }

    /**
     * Returns a list of objects (static function).
     */
    public function readList($options = [], $values = [])
    {
        $values = (isset($options['values'])) ? $options['values'] : $values;
        $fields = (isset($options['fields'])) ? $options['fields'] : '*';
        $where = (isset($options['where']) && $options['where'] != '') ? $options['where'] : '1=1';
        $order = (isset($options['order']) && $options['order'] != '') ? ' ORDER BY ' . $options['order'] : '';
        $limit = (isset($options['limit']) && $options['limit'] != '') ? ' LIMIT ' . $options['limit'] : '';
        $query = 'SELECT ' . $fields . $this->fieldPoints() . '
                    FROM ' . $this->tableName . '
                    WHERE ' . $where . '
                    ' . $order . '
                    ' . $limit;
        $result = Db::returnAll($query, $values);
        $list = [];
        $completeList = (isset($options['completeList'])) ? $options['completeList'] : true;
        foreach ($result as $item) {
            $itemComplete = new $this->className($item);
            if ($completeList) {
                $list[] = $itemComplete;
            } else {
                $list[] = $itemComplete->values;
            }
        }
        return $list;
    }

    /**
     * Returns a list using a query.
     */
    public function readListQuery($query, $values = [])
    {
        $query = str_replace('##', ASTERION_DB_PREFIX, $query);
        $result = Db::returnAll($query, $values);
        $list = [];
        foreach ($result as $name) {
            $list[] = new $this->className($name);
        }
        return $list;
    }

    /**
     * Insert values to an object, checks if it already exists to modify it instead.
     */
    public function insert($values, $options = [])
    {
        if (count($values) > 0) {
            $values = $this->formatMultipleValues($values);
            if (isset($values[$this->primary])) {
                $object = $this->read($values[$this->primary]);
            }
            if ($this->id() != '') {
                $object = $this->read($this->id());
            }
            if (isset($object) && $object->id() != '') {
                $this->setId($object->id());
                return $this->modify($values);
            } else {
                $queryOrd = '';
                if ($this->hasOrd() && (!isset($values['ord']) || $values['ord'] == '')) {
                    $query = 'SELECT MAX(ord) as maxOrd FROM ' . $this->tableName;
                    $maxOrdResult = Db::returnSingle($query);
                    $maxOrd = (isset($maxOrdResult['maxOrd'])) ? intval($maxOrdResult['maxOrd']) + 1 : 1;
                    $this->set('ord', $maxOrd);
                    $queryOrd = '`ord`=' . $maxOrd . ',';
                }
                $this->set('created', date("Y-m-d H:i:s"));
                $this->set('modified', date("Y-m-d H:i:s"));
                $queryCreated = ($this->hasCreated()) ? '`created`="' . $this->get('created') . '",' : '';
                $queryModified = ($this->hasModified()) ? '`modified`="' . $this->get('created') . '",' : '';
                $createSet = $this->createSet($values);
                if ($createSet['query'] != '') {
                    $query = 'INSERT INTO ' . $this->tableName . '
                                SET
                                ' . $queryCreated . '
                                ' . $queryModified . '
                                ' . $queryOrd . '
                                ' . $createSet['query'];
                    Db::execute($query, $createSet['setValues']);
                    if ($this->hasIdAutoIncrement()) {
                        $queryLastId = 'SELECT LAST_INSERT_ID() AS lastId;';
                        $lastId = Db::returnSingle($queryLastId);
                        $this->setId(intval($lastId['lastId']));
                    }
                    $this->uploadFiles($values);
                    $simpleQuery = (isset($options['simpleQuery'])) ? $options['simpleQuery'] : false;
                    if ($simpleQuery != true) {
                        $this->insertMultiple($values, $options);
                        $this->reloadObject();
                    }
                }
            }
        }
    }

    /**
     * Modify the values of an object.
     */
    public function modify($values, $options = [])
    {
        if (count($values) > 0 && $this->id() != '') {
            $values = $this->formatMultipleValues($values);
            $queryModified = '';
            if ($this->hasModified()) {
                $this->set('modified', date("Y-m-d H:i:s"));
                $queryModified = '`modified`="' . $this->get('modified') . '",';
            }
            $complete = (isset($options['complete'])) ? $options['complete'] : true;
            $createSet = $this->createSet($values, $complete);
            if ($createSet['query'] != '') {
                $primary = $this->primary;
                $idItem = (isset($values[$primary . '_oldId'])) ? $values[$primary . '_oldId'] : $this->id();
                $query = 'UPDATE ' . $this->tableName . '
                            SET
                            ' . $queryModified . '
                            ' . $createSet['query'] . '
                            WHERE ' . $this->primary . '="' . $idItem . '"';
                Db::execute($query, $createSet['setValues']);
            }
            $this->uploadFiles($values);
            $simpleQuery = (isset($options['simpleQuery'])) ? $options['simpleQuery'] : false;
            if ($simpleQuery != true) {
                $this->insertMultiple($values, $options);
                $this->reloadObject();
            }
        }
    }

    /**
     * Update the values of an object.
     */
    public function update($values, $options = [])
    {
        $options['complete'] = false;
        return $this->modify($values, $options);
    }

    /**
     * Modify a single attribute.
     */
    public function modifySimple($attribute, $value)
    {
        Db::execute('UPDATE ' . $this->tableName . '
                        SET ' . $attribute . ' = :' . $attribute . '
                        WHERE ' . $this->primary . ' = :' . $this->primary,
            [$attribute => $value, $this->primary => $this->id()]);
    }

    /**
     * Format the values when the type of the attribute is multiple.
     */
    public function formatMultipleValues($values)
    {
        foreach ($this->info->attributes->attribute as $item) {
            $name = (string) $item->name;
            $type = (string) $item->type;
            if ($type == 'multiple_object' && isset($values[$name]) && is_array($values[$name])) {
                foreach ($values[$name] as $key => $arrayObject) {
                    $values[$name][$key]['refMultiple'] = $name . '-' . $key;
                }
            }
        }
        return $values;
    }

    /**
     * Insert the values related to an object.
     */
    public function insertMultiple($values, $options = [])
    {
        foreach ($this->info->attributes->attribute as $item) {
            $name = (string) $item->name;
            $type = (string) $item->type;
            switch ($type) {
                case 'multiple_object':
                    $refObject = (string) $item->refObject;
                    $lnkAttribute = (string) $item->lnkAttribute;
                    if (isset($values[$name]) && is_array($values[$name])) {
                        foreach ($values[$name] as $itemMultiple) {
                            //If it's an object
                            if (is_object($itemMultiple)) {
                                $itemMultiple->modifySimple($lnkAttribute, $this->id());
                            }
                            //If it's an array
                            if (is_array($itemMultiple)) {
                                $itemMultipleObject = new $refObject();
                                $itemMultipleObject->insert($itemMultiple);
                                $itemMultipleObject->modifySimple($lnkAttribute, $this->id());
                            }
                        }
                    }
                    break;
                case 'multiple_autocomplete':
                    $refObject = (string) $item->refObject;
                    $lnkObject = (string) $item->lnkObject;
                    $refAttribute = (string) $item->refAttribute;
                    $refObjectIns = new $refObject();
                    $lnkObjectIns = new $lnkObject();
                    $complete = (isset($options['complete'])) ? $options['complete'] : true;
                    if ($complete) {
                        Db::execute('DELETE FROM ' . Db::prefixTable($lnkObjectIns->className) . ' WHERE ' . $this->primary . '="' . $this->id() . '"');
                    }
                    if (isset($values[$name]) && $values[$name] != '') {
                        $autocompleteItems = explode(',', $values[$name]);
                        foreach ($autocompleteItems as $autocompleteItem) {
                            $autocompleteItem = trim($autocompleteItem);
                            if ($autocompleteItem != '') {
                                //Check if it already exists
                                $autocompleteObject = $refObjectIns->readFirstObject(['where' => 'BINARY ' . $refAttribute . '="' . $autocompleteItem . '"']);
                                if ($autocompleteObject->id() == '') {
                                    $autocompleteObject = new $refObject();
                                    $autocompleteObject->insert([$refAttribute => $autocompleteItem]);
                                }
                                //Check if the link exists and make it
                                $lnkObjectIns = $lnkObjectIns->readFirstObject(['where' => $this->primary . '=' . $this->id() . ' AND ' . $refObjectIns->primary . '=' . $autocompleteObject->id()]);
                                if ($lnkObjectIns->get($this->primary) == '') {
                                    $lnkObjectIns->insert([$this->primary => $this->id(),
                                        $refObjectIns->primary => $autocompleteObject->id()],
                                        ['simpleQuery' => true]);
                                }
                            }
                        }
                    }
                    break;
                case 'multiple_checkbox':
                case 'multiple-select':
                    $complete = (isset($options['complete'])) ? $options['complete'] : true;
                    $refObject = (string) $item->refObject;
                    $refObjectIns = new $refObject();
                    if ((string) $item->lnkObject != '') {
                        $lnkObject = (string) $item->lnkObject;
                        $lnkObjectIns = new $lnkObject();
                        if ($complete) {
                            Db::execute('DELETE FROM ' . Db::prefixTable($lnkObjectIns->className) . ' WHERE ' . $this->primary . '="' . $this->id() . '"');
                        }
                        if (isset($values[$name]) && is_array($values[$name])) {
                            foreach ($values[$name] as $key => $itemMultiple) {
                                //If it's an object
                                if (is_object($itemMultiple)) {
                                    $objectExists = $lnkObjectIns->readFirstObject(['where' => $this->primary . '="' . $this->id() . '" AND ' . $refObjectIns->primary . '="' . $itemMultiple->id() . '"']);
                                    if ($objectExists->get($this->primary) == '') {
                                        $lnkObjectIns->insert([$this->primary => $this->id(),
                                            $refObjectIns->primary => $itemMultiple->id()],
                                            ['simpleQuery' => true]);
                                    }
                                } else if (is_array($itemMultiple)) {
                                    //If it's an array
                                    $refObjectNew = new $refObject();
                                    $refObjectNew->insert($itemMultiple);
                                    $lnkObjectIns->insert([$this->primary => $this->id(),
                                        $refObjectIns->primary => $refObjectNew->id()],
                                        ['simpleQuery' => true]);
                                } else if ($itemMultiple == 'on') {
                                    //If it's just a "on" checkbox
                                    $lnkObjectExists = new $lnkObject();
                                    $lnkObjectExists = $lnkObjectIns->readFirstObject(['where' => $this->primary . '="' . $this->id() . '" AND ' . $refObjectIns->primary . '="' . $key . '"']);
                                    if ($lnkObjectExists->get($this->primary) == '') {
                                        $lnkObjectNew = new $lnkObject();
                                        $lnkObjectNew->insert([$this->primary => $this->id(), $refObjectIns->primary => $key], ['simpleQuery' => true]);
                                    }
                                } else {
                                    // If it's just an id from a multiple select
                                    $lnkObjectExists = new $lnkObject();
                                    $lnkObjectExists = $lnkObjectIns->readFirstObject(['where' => $this->primary . '="' . $this->id() . '" AND ' . $refObjectIns->primary . '="' . $itemMultiple . '"']);
                                    if ($lnkObjectExists->get($this->primary) == '') {
                                        $lnkObjectNew = new $lnkObject();
                                        $lnkObjectNew->insert([$this->primary => $this->id(), $refObjectIns->primary => $itemMultiple], ['simpleQuery' => true]);
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($values[$name]) && is_array($values[$name])) {
                            if ($complete) {
                                Db::execute('UPDATE ' . Db::prefixTable($refObject) . ' SET ' . $this->primary . '=NULL WHERE ' . $this->primary . '="' . $this->id() . '"');
                            }
                            foreach ($values[$name] as $key => $itemMultiple) {
                                if ($itemMultiple == 'on') {
                                    $refObjectUpdate = new $refObject();
                                    $refObjectUpdate = $refObjectUpdate->read($key);
                                    $refObjectUpdate->modifySimple($this->primary, $this->id());
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Delete an object and related values in multiple tables.
     */
    public function delete()
    {
        if ($this->id() != '') {
            $this->deleteFiles();
            $query = 'DELETE FROM ' . $this->tableName . '
                        WHERE ' . $this->primary . '="' . $this->id() . '"';
            Db::execute($query);
            $onDelete = (string) $this->info->info->sql->onDelete;
            if ($onDelete != '') {
                $onDeleteFields = explode(',', $onDelete);
                foreach ($onDeleteFields as $onDeleteField) {
                    $onDeleteObject = new $onDeleteField;
                    $listObjects = $onDeleteObject->readList(['where' => $this->primary . '="' . $this->id() . '"']);
                    foreach ($listObjects as $listObject) {
                        $listObject->delete();
                    }
                }
            }
        }
    }

    /**
     * Returns a SQL string in case of points.
     */
    public function fieldPoints()
    {
        $points = $this->info->xpath('//type[.="point"]/parent::*');
        $fields = '';
        foreach ($points as $point) {
            $pointName = (string) $point->name;
            $fields .= ', CONCAT(X(' . $pointName . '),":",Y(' . $pointName . ')) AS ' . $pointName;
        }
        return $fields;
    }

    /**
     * Update the order of a list of objects.
     */
    public function updateOrder($values)
    {
        $id = (string) $this->primary;
        $i = 1;
        foreach ($values as $value) {
            $query = 'UPDATE ' . $this->tableName . '
                        SET ord=' . $i . '
                        WHERE ' . $id . '="' . $value . '"';
            Db::execute($query);
            $i++;
        }
    }

    /**
     * Drops a table from the database.
     */
    public function dropTable()
    {
        $query = 'DROP TABLE IF EXISTS `' . $this->tableName . '`';
        Db::execute($query);
    }

    /**
     * Creates the table using the information in the XML file.
     */
    public function createTable()
    {
        $existsQuery = 'SHOW TABLES LIKE "' . $this->tableName . '"';
        $exists = Db::returnAll($existsQuery);
        if (count($exists) == 0) {
            $query = $this->createTableQuery();
            Db::execute($query);
            $this->createTableIndexes();
        }
    }

    /**
     * Get the query to create the table using the information in the XML file.
     */
    public function createTableQuery()
    {
        $query = 'CREATE TABLE `' . $this->tableName . '` (';
        $query .= ($this->info->info->sql->order == 'true') ? '`ord` int(10) unsigned DEFAULT NULL,' : '';
        $query .= ($this->info->info->sql->created == 'true') ? '`created` DATETIME DEFAULT NULL,' : '';
        $query .= ($this->info->info->sql->modified == 'true') ? '`modified` DATETIME DEFAULT NULL,' : '';
        $queryFields = '';
        foreach ($this->getAttributes() as $attribute) {
            $queryFields .= Db_ObjectType::createAttributeSql($attribute);
        }
        $engine = ((string) $this->info->info->sql->engine != '') ? (string) $this->info->info->sql->engine : 'MyISAM';
        $query .= substr($queryFields, 0, -1) . ') ENGINE=' . $engine . ' COLLATE utf8_unicode_ci;';
        return $query;
    }

    /**
     * Get the query to add an attribute to the table.
     */
    public function updateAttributeQuery($attribute, $language = '')
    {
        return 'ALTER TABLE `' . $this->tableName . '` ADD ' . Db_ObjectType::createAttributeSqlSimple($attribute, $language) . ';';
    }

    /**
     * Creates the table indexes defined in the class XML file.
     */
    public function createTableIndexes()
    {
        Db::executeMultiple($this->createTableIndexesQuery());
    }

    /**
     * Get the query to create the table indexes defined in the class XML file.
     */
    public function createTableIndexesQuery()
    {
        $queries = [];
        if (isset($this->info->indexes)) {
            foreach ($this->info->indexes->index as $item) {
                $name = (string) $item->name;
                $type = (string) $item->type;
                $fields = (string) $item->fields;
                $lang = (string) $item->language;
                if ($lang == 'true') {
                    foreach (Language::languages() as $language) {
                        $name = (string) $item->name . '_' . $language['id'];
                        $query = 'SHOW INDEX FROM ' . $this->tableName . ' WHERE KEY_NAME="' . $name . '"';
                        if (count(Db::returnAll($query)) == 0) {
                            $queries[] = 'CREATE ' . $type . ' INDEX `' . $name . '` ON ' . $this->tableName . ' (`' . $name . '`)';
                        }
                    }
                } else {
                    $query = 'SHOW INDEX FROM ' . $this->tableName . ' WHERE KEY_NAME="' . $name . '"';
                    if (count(Db::returnAll($query)) == 0) {
                        $queries[] = 'CREATE ' . $type . ' INDEX `' . $name . '` ON ' . $this->tableName . ' (' . $fields . ')';
                    }
                }
            }
        }
        return $queries;
    }

    /**
     * Creates a SET string used in the insertion and modification of the values in the DB.
     */
    public function createSet($values, $complete = true)
    {
        $query = '';
        $setValues = [];
        if (isset($values['ord']) && $values['ord'] != '') {
            $setValues['ord'] = $values['ord'];
            $query .= '`ord` = :ord, ';
        }
        foreach ($this->info->attributes->attribute as $item) {
            $name = (string) $item->name;
            $type = (string) $item->type;
            switch ($type) {
                default:
                    if ((string) $item->language == 'true') {
                        foreach (Language::languages() as $language) {
                            $nameLanguage = $name . '_' . $language['id'];
                            if (isset($values[$nameLanguage])) {
                                $setValues[$nameLanguage] = $values[$nameLanguage];
                                $query .= '`' . $nameLanguage . '` = :' . $nameLanguage . ', ';
                            }
                        }
                    } else {
                        if (isset($values[$name])) {
                            $setValues[$name] = $values[$name];
                            $query .= '`' . $name . '` = :' . $name . ', ';
                        }
                    }
                    break;
                case 'id_char32':
                    if ($this->id() == '' && (!isset($values[$name]) || $values[$name] == '')) {
                        $idMd5 = md5(microtime() * rand() * rand());
                        $setValues[$name] = $idMd5;
                        $query .= '`' . $name . '` = :' . $name . ', ';
                        $this->setId($idMd5);
                    }
                    break;
                case 'id_varchar':
                    if (isset($values[$name])) {
                        $setValues[$name] = Text::simpleUrl($values[$name], '');
                        $query .= '`' . $name . '` = :' . $name . ', ';
                        $this->setId($setValues[$name]);
                    }
                    break;
                case 'password':
                    if (isset($values[$name])) {
                        $password = hash('sha256', $values[$name]);
                        $setValues[$name] = $password;
                        $query .= '`' . $name . '` = :' . $name . ', ';
                    }
                    if (isset($values[$name . '_new']) && $values[$name . '_new'] != '') {
                        $password = hash('sha256', $values[$name . '_new']);
                        $setValues[$name] = $password;
                        $query .= '`' . $name . '` = :' . $name . '", ';
                    }
                    break;
                case 'hidden_url':
                    if ((string) $item->language == 'true') {
                        foreach (Language::languages() as $language) {
                            $textUrl = (string) $item->refAttribute . '_' . $language['id'];
                            $nameLanguage = $name . '_' . $language['id'];
                            if (isset($values[$textUrl])) {
                                $setValues[$nameLanguage] = Text::simple($values[$textUrl]);
                                $query .= '`' . $nameLanguage . '` = :' . $nameLanguage . ', ';
                            }
                        }
                    } else {
                        $textUrl = (string) $item->refAttribute;
                        if (isset($values[$textUrl])) {
                            $setValues[$name] = Text::simple($values[$textUrl]);
                            $query .= '`' . $name . '` = :' . $name . ', ';
                        }
                    }
                    break;
                case 'hidden_user_admin':
                    if (isset($values[$name . '_force'])) {
                        $setValues[$name] = $values[$name . '_force'];
                        $query .= $name . ' = :' . $name . ', ';
                    } elseif (!isset($values[$name]) || $values[$name] == '') {
                        $userAdminLogged = UserAdmin_Login::getInstance();
                        $setValues[$name] = $userAdminLogged->id();
                        $query .= $name . ' = :' . $name . ', ';
                    }
                    break;
                case 'point':
                    $pointLat = (isset($values[$name . '_lat'])) ? floatval($values[$name . '_lat']) : 0;
                    $pointLng = (isset($values[$name . '_lng'])) ? floatval($values[$name . '_lng']) : 0;
                    $query .= (isset($values[$name . '_lat']) && isset($values[$name . '_lng'])) ? '`' . $name . '`=POINT(' . $pointLat . ', ' . $pointLng . '), ' : '';
                    break;
                case 'text_code':
                    $query .= (isset($values[$name]) && $values[$name] != "") ? '`' . $name . '`="' . Text::simpleCode($values[$name]) . '", ' : '';
                    break;
                case 'text_double':
                    $query .= (isset($values[$name]) && $values[$name] != "") ? '`' . $name . '`="' . floatval($values[$name]) . '", ' : '';
                    break;
                case 'text_integer':
                    $query .= (isset($values[$name]) && $values[$name] != "") ? '`' . $name . '`="' . intval($values[$name]) . '", ' : '';
                    break;
                case 'date':
                case 'date_complete':
                case 'date_hour':
                case 'date_text':
                case 'date-checkbox':
                    if ($type == 'date-checkbox') {
                        if ($complete) {
                            $nameCheckbox = $name . '_checkbox';
                            $values[$nameCheckbox] = (isset($values[$nameCheckbox])) ? $values[$nameCheckbox] : 0;
                            $values[$nameCheckbox] = ($values[$nameCheckbox] === "on") ? 1 : $values[$nameCheckbox];
                            if ($values[$nameCheckbox] == '1') {
                                if (isset($values[$name]) && !isset($values[$name . 'yea'])) {
                                    $query .= '`' . $name . '`="' . $values[$name] . '", ';
                                } else {
                                    $yea = isset($values[$name . 'yea']) ? str_pad(intval($values[$name . 'yea']), 2, "0", STR_PAD_LEFT) : 0;
                                    $mon = isset($values[$name . 'mon']) ? str_pad(intval($values[$name . 'mon']), 2, "0", STR_PAD_LEFT) : 0;
                                    $day = isset($values[$name . 'day']) ? str_pad(intval($values[$name . 'day']), 2, "0", STR_PAD_LEFT) : 0;
                                    $hou = isset($values[$name . 'hou']) ? str_pad(intval($values[$name . 'hou']), 2, "0", STR_PAD_LEFT) : 0;
                                    $min = isset($values[$name . 'min']) ? str_pad(intval($values[$name . 'min']), 2, "0", STR_PAD_LEFT) : 0;
                                    $date = $yea . '-' . $mon . '-' . $day . ' ' . $hou . ':' . $min . ':00';
                                    $query .= isset($values[$name . 'yea']) ? '`' . $name . '`="' . $date . '", ' : '';
                                }
                            } else {
                                $query .= '`' . $name . '`=NULL, ';
                            }
                        }
                    } else {
                        if (isset($values[$name]) && !isset($values[$name . 'yea'])) {
                            $query .= '`' . $name . '`="' . $values[$name] . '", ';
                        } else {
                            $yea = isset($values[$name . 'yea']) ? str_pad(intval($values[$name . 'yea']), 2, "0", STR_PAD_LEFT) : 0;
                            $mon = isset($values[$name . 'mon']) ? str_pad(intval($values[$name . 'mon']), 2, "0", STR_PAD_LEFT) : 0;
                            $day = isset($values[$name . 'day']) ? str_pad(intval($values[$name . 'day']), 2, "0", STR_PAD_LEFT) : 0;
                            $hou = isset($values[$name . 'hou']) ? str_pad(intval($values[$name . 'hou']), 2, "0", STR_PAD_LEFT) : 0;
                            $min = isset($values[$name . 'min']) ? str_pad(intval($values[$name . 'min']), 2, "0", STR_PAD_LEFT) : 0;
                            $date = $yea . '-' . $mon . '-' . $day . ' ' . $hou . ':' . $min . ':00';
                            $query .= (isset($values[$name . 'yea']) || isset($values[$name . 'hou'])) ? '`' . $name . '`="' . $date . '", ' : '';
                        }
                    }
                    break;
                case 'date_text':
                    if (isset($values[$name])) {
                        $valueDate = $values[$name];
                        $valueDateInfo = explode('-', $valueDate);
                        if (isset($valueDateInfo[2])) {
                            $valueDate = intval($valueDateInfo[2]) . '-' . intval($valueDateInfo[1]) . '-' . intval($valueDateInfo[0]);
                        } else {
                            $valueDate = '0000-00-00';
                        }
                        $query .= '`' . $name . '`="' . $valueDate . '", ';
                    }
                    break;
                case 'checkbox':
                    if ($complete) {
                        $values[$name] = (isset($values[$name])) ? $values[$name] : 0;
                        $values[$name] = ($values[$name] === "on") ? 1 : $values[$name];
                        $query .= isset($values[$name]) ? '`' . $name . '`="' . $values[$name] . '", ' : '`' . $name . '`=NULL, ';
                    }
                    break;
                case 'select_checkbox':
                    if ($complete) {
                        $nameCheckbox = $name . '_checkbox';
                        $values[$nameCheckbox] = (isset($values[$nameCheckbox])) ? $values[$nameCheckbox] : 0;
                        $values[$nameCheckbox] = ($values[$nameCheckbox] === "on") ? 1 : $values[$nameCheckbox];
                        $query .= ($values[$nameCheckbox] == '1' && isset($values[$name])) ? '`' . $name . '`="' . $values[$name] . '", ' : '`' . $name . '`=NULL, ';
                    }
                    break;
                case 'id_autoincrement':
                case 'file':
                case 'multiple_object':
                case 'multiple_autocomplete':
                case 'multiple_checkbox':
                case 'multiple-select':
                    break;
            }
        }
        $query = ($query != '') ? substr($query, 0, -2) : $query;
        return ['query' => $query, 'setValues' => $setValues];
    }

    /**
     * Upload the files of an object according the its attributes.
     */
    public function uploadFiles($values = [])
    {
        $fields = $this->info->xpath('//type[.="file"]/parent::*');
        foreach ($fields as $field) {
            $fieldName = (string) $field->name;
            // Upload from the FILES array
            if (isset($values['refMultiple']) && $values['refMultiple'] != '') {
                $fieldNameMultiple = $values['refMultiple'] . '-' . $fieldName;
                // Case multiple
                if (isset($_FILES[$fieldNameMultiple]) && isset($_FILES[$fieldNameMultiple]['tmp_name']) && $_FILES[$fieldNameMultiple]['tmp_name'] != '') {
                    if ((string) $field->mode == 'image') {
                        $fileTmp = $_FILES[$fieldNameMultiple]['tmp_name'];
                        $fileSave = Text::simpleUrlFileBase($this->id() . '_' . $fieldName);
                        if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                            $this->modifySimple($fieldName, $fileSave);
                        }
                        unset($_FILES[$fieldNameMultiple]);
                    } else {
                        $fileTmp = $_FILES[$fieldNameMultiple]['tmp_name'];
                        $fileSave = $this->id() . '_' . Text::simpleUrlFile($_FILES[$fieldNameMultiple]['name']);
                        if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                            $this->modifySimple($fieldName, $fileSave);
                        }
                        unset($_FILES[$fieldNameMultiple]);
                    }
                }
            }
            // Case single
            if (isset($_FILES[$fieldName]) && isset($_FILES[$fieldName]['tmp_name']) && $_FILES[$fieldName]['tmp_name'] != '') {
                if (is_array($_FILES[$fieldName]['tmp_name'])) {
                    // Multiple files
                    $filesArray = [];
                    for ($i = 0; $i < count($_FILES[$fieldName]['tmp_name']); $i++) {
                        $filesArray[] = ['name' => (isset($_FILES[$fieldName]['name'][$i]) ? $_FILES[$fieldName]['name'][$i] : ''),
                            'tmp_name' => (isset($_FILES[$fieldName]['tmp_name'][$i]) ? $_FILES[$fieldName]['tmp_name'][$i] : ''),
                            'type' => (isset($_FILES[$fieldName]['type'][$i]) ? $_FILES[$fieldName]['type'][$i] : ''),
                            'error' => (isset($_FILES[$fieldName]['error'][$i]) ? $_FILES[$fieldName]['error'][$i] : ''),
                            'size' => (isset($_FILES[$fieldName]['size'][$i]) ? $_FILES[$fieldName]['size'][$i] : '')];
                    }
                    $filesSaved = [];
                    foreach ($filesArray as $key => $fileItem) {
                        $fileTmp = $fileItem['tmp_name'];
                        $fileName = $fileItem['name'];
                        switch (File::fileExtension($fileName)) {
                            default:
                                $fileSave = $this->id() . '_' . Text::simpleUrlFile($_FILES[$fieldName]['name']) . '-' . $key;
                                if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                                    $filesSaved[] = $fileSave;
                                }
                                break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                            case 'gif':
                                $fileSave = Text::simpleUrlFileBase($this->id() . '_' . $fieldName) . '-' . $key;
                                if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                                    $filesSaved[] = $fileSave;
                                }
                                break;
                        }
                    }
                    if (count($filesSaved) > 0) {
                        $this->modifySimple($fieldName, implode(':', $filesSaved));
                    }

                    unset($_FILES[$fieldName]);
                } elseif ((string) $field->mode == 'adaptable') {
                    // Image and file
                    $fileTmp = $_FILES[$fieldName]['tmp_name'];
                    $fileName = $_FILES[$fieldName]['name'];
                    switch (File::fileExtension($fileName)) {
                        default:
                            $fileSave = $this->id() . '_' . Text::simpleUrlFile($_FILES[$fieldName]['name']);
                            if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                                $this->modifySimple($fieldName, $fileSave);
                            }
                            break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                            $fileSave = Text::simpleUrlFileBase($this->id() . '_' . $fieldName);
                            if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                                $this->modifySimple($fieldName, $fileSave);
                            }
                            break;
                    }
                    unset($_FILES[$fieldName]);
                } elseif ((string) $field->mode == 'image') {
                    // Image only
                    $fileTmp = $_FILES[$fieldName]['tmp_name'];
                    $fileSave = Text::simpleUrlFileBase($this->id() . '_' . $fieldName);
                    if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                    unset($_FILES[$fieldName]);
                } else {
                    // File only
                    $fileTmp = $_FILES[$fieldName]['tmp_name'];
                    $fileSave = $this->id() . '_' . Text::simpleUrlFile($_FILES[$fieldName]['name']);
                    if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                    unset($_FILES[$fieldName]);
                }
            }
            // Upload from an URL
            if (isset($values[$fieldName]) && is_string($values[$fieldName])) {
                if ((string) $field->mode == 'image') {
                    $fileSave = Text::simpleUrlFileBase($this->id() . '_' . $fieldName);
                    if (Image_File::saveImageUrl($values[$fieldName], $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                } else {
                    $fileSave = $this->id() . '_' . $fieldName . '.' . File::urlExtension($values[$fieldName]);
                    if (File::uploadUrl($values[$fieldName], $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                }
            }
        }
    }

    /**
     * Delete files from the server.
     */
    public function deleteFiles()
    {
        foreach ($this->info->attributes->attribute as $item) {
            if ((string) $item->type == "file") {
                $name = Text::simpleUrlFileBase((string) $item->name);
                if ((string) $item->mode == 'image') {
                    Image_File::delete_image($this->className, $this->id() . '-' . $name);
                } else {
                    $file = ASTERION_STOCK_FILE . $this->className . 'Files/' . $this->get($name);
                    @unlink($file);
                }
            }
        }
    }

}
