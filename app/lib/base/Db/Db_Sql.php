<?php
/**
* @class DbSql
*
* This is the class that connects the object with the database in a logical level.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Db_Sql {

    /**
    * Construct the object.
    */
    public function __construct($values=array()) {
        $this->className = get_class($this);
        $this->tableName = Db::prefixTable($this->className);
        $this->info = XML::readClass($this->className);
        $this->primary = (string)$this->info->info->sql->primary;
        $this->title = __((string)$this->info->info->form->title);
        $ord = (isset($values['ord'])) ? $values['ord'] : '';
        $created = (isset($values['created'])) ? $values['created'] : '';
        $modified = (isset($values['modified'])) ? $values['modified'] : '';
        $this->values = array('created'=>$created, 'modified'=>$modified, 'ord'=>$ord);
    }

    /**
    * Counts the number of objects in the DB (static function).
    */
    static public function countResults($options=array(), $values=array()) {
        $values = (isset($options['values'])) ? $options['values'] : $values;
        $table = (isset($options['table'])) ? $options['table'] : get_called_class();
        $where = (isset($options['where']) && $options['where']!='') ? $options['where'] : '1=1';
        $query = 'SELECT COUNT(*) AS numElements
                        FROM '.Db::prefixTable($table).'
                        WHERE '.$where;
        $result = Db::returnSingle($query, $values);
        return $result['numElements'];
    }

    /**
    * Counts the number of objects in the DB.
    */
    public function countResultsObject($options=array(), $values=array()) {
        $options['table'] = (isset($options['table']) && $options['table']!='') ? $options['table'] : $this->className;
        return Db_Sql::countResults($options, $values);
    }

    /**
    * Returns the values of an object (static function).
    */
    static public function readValues($id, $options=array()) {
        $table = (isset($options['table'])) ? $options['table'] : get_called_class();
        $fields = (isset($options['fields'])) ? $options['fields'] : '*';
        $object = new $table();
        $query = 'SELECT '.$fields.$object->fieldPoints().'
                    FROM '.Db::prefixTable($table).'
                    WHERE '.$object->primary.'=:idObject';
        return Db::returnSingle($query, array('idObject' => $id));
    }

    /**
    * Returns the values of an object (static function).
    */
    public function readObjectValues($id, $options=array()) {
        $options['table'] = $this->className;
        return Db_Sql::readValues($id, $options);
    }

    /**
    * Returns a single object using its id (static function).
    */
    static public function read($id, $options=array()) {
        $table = (isset($options['table'])) ? $options['table'] : get_called_class();
        $options['table'] = $table;
        $values = Db_Sql::readValues($id, $options);
        return new $table($values);
    }

    /**
    * Returns a single object using its id.
    */
    public function readObject($id, $options=array()) {
        $options['table'] = $this->className;
        return Db_Sql::read($id, $options);
    }

    /**
    * Returns a single object (static function).
    */
    public static function readFirst($options=array(), $values=array()) {
        $values = (isset($options['values'])) ? $options['values'] : $values;
        $table = (isset($options['table'])) ? $options['table'] : get_called_class();
        $fields = (isset($options['fields'])) ? $options['fields'] : '*';
        $where = (isset($options['where']) && $options['where']!='') ? $options['where'] : '1=1';
        $order = (isset($options['order']) && $options['order']!='') ? ' ORDER BY '.$options['order'] : '';
        $limit = (isset($options['limit']) && $options['limit']!='') ? ' LIMIT '.$options['limit'].',1' : ' LIMIT 1';
        $object = new $table();
        $query = 'SELECT '.$fields.$object->fieldPoints().'
                    FROM '.Db::prefixTable($table).'
                    WHERE '.$where.'
                    '.$order.'
                    '.$limit;
        return new $table(Db::returnSingle($query, $values));
    }

    /**
    * Returns a single object.
    */
    public function readFirstObject($options=array(), $values=array()) {
        $options['table'] = $this->className;
        return Db_Sql::readFirst($options, $values);
    }

    /**
    * Returns a list of objects (static function).
    */
    public static function readList($options=array(), $values=array()) {
        $values = (isset($options['values'])) ? $options['values'] : $values;
        $table = (isset($options['table'])) ? $options['table'] : get_called_class();
        $fields = (isset($options['fields'])) ? $options['fields'] : '*';
        $where = (isset($options['where']) && $options['where']!='') ? $options['where'] : '1=1';
        $order = (isset($options['order']) && $options['order']!='') ? ' ORDER BY '.$options['order'] : '';
        $limit = (isset($options['limit']) && $options['limit']!='') ? ' LIMIT '.$options['limit'] : '';
        $objectType = (isset($options['object'])) ? $options['object'] : $table;
        $object = new $objectType();
        $query = 'SELECT '.$fields.$object->fieldPoints().'
                    FROM '.Db::prefixTable($table).'
                    WHERE '.$where.'
                    '.$order.'
                    '.$limit;
        $result = Db::returnAll($query, $values);
        $list = array();
        $completeList = (isset($options['completeList'])) ? $options['completeList'] : true;
        foreach ($result as $item) {
            $itemComplete = new $objectType($item);
            if ($completeList) {
                $list[] = $itemComplete;
            } else {
                $list[] = $itemComplete->values;
            }
        }
        return $list;
    }

    /**
    * Returns a list of objects.
    */
    public function readListObject($options=array(), $values=array()) {
        $options['table'] = (isset($options['table']) && $options['table']!='') ? $options['table'] : $this->className;
        return Db_Sql::readList($options, $values);
    }

    /**
    * Returns a list using a query.
    */
    public function readListQuery($query, $values=array()) {
        $objectType = $this->className;
        $query = str_replace('##', DB_PREFIX, $query);
        $result = Db::returnAll($query, $values);
        $list = array();
        foreach ($result as $name) {
            $list[] = new $objectType($name);
        }
        return $list;
    }

    /**
    * Insert values to an object, checks if it already exists to modify it instead.
    */
    public function insert($values, $options=array()) {
        if (count($values)>0) {
            $values = $this->formatMultipleValues($values);
            if (isset($values[$this->primary])) {
                $object = $this->readObject($values[$this->primary]);
            }
            if ($this->id()!='') {
                $object = $this->readObject($this->id());
            }
            if (isset($object) && $object->id() != '') {
                $this->setId($object->id());
                return $this->modify($values);
            } else {
                $queryOrd = '';
                if ($this->hasOrd() && (!isset($values['ord']) || $values['ord']=='')) {
                    $query = 'SELECT MAX(ord) as maxOrd FROM '.$this->tableName;
                    $maxOrdResult = Db::returnSingle($query);
                    $maxOrd = (isset($maxOrdResult['maxOrd'])) ? intval($maxOrdResult['maxOrd'])+1 : 1;
                    $this->set('ord', $maxOrd);
                    $queryOrd = '`ord`='.$maxOrd.',';
                }
                $this->set('created', date("Y-m-d H:i:s"));
                $this->set('modified', date("Y-m-d H:i:s"));
                $queryCreated = ($this->hasCreated()) ? '`created`="'.$this->get('created').'",' : '';
                $queryModified = ($this->hasModified()) ? '`modified`="'.$this->get('created').'",' : '';
                $createSet = $this->createSet($values);
                if ($createSet['query']!='') {
                    $query = 'INSERT INTO '.$this->tableName.'
                                SET
                                '.$queryCreated.'
                                '.$queryModified.'
                                '.$queryOrd.'
                                '.$createSet['query'];
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
    public function modify($values, $options=array()) {
        if (count($values)>0 && $this->id()!='') {
            $values = $this->formatMultipleValues($values);
            $queryModified = '';
            if ($this->hasModified()) {
                $this->set('modified', date("Y-m-d H:i:s"));
                $queryModified = '`modified`="'.$this->get('modified').'",';
            }
            $complete = (isset($options['complete'])) ? $options['complete'] : true;
            $createSet = $this->createSet($values, $complete);
            if ($createSet['query']!='') {
                $primary = $this->primary;
                $idItem = (isset($values[$primary.'_oldId'])) ? $values[$primary.'_oldId'] : $this->id();
                $query = 'UPDATE '.$this->tableName.'
                            SET
                            '.$queryModified.'
                            '.$createSet['query'].'
                            WHERE '.$this->primary.'="'.$idItem.'"';
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
    public function update($values, $options=array()) {
        $options['complete'] = false;
        return $this->modify($values, $options);
    }

    /**
    * Modify a single attribute.
    */
    public function modifySimple($attribute, $value) {
        Db::execute('UPDATE '.$this->tableName.'
                        SET '.$attribute.' = :'.$attribute.'
                        WHERE '.$this->primary.' = :'.$this->primary,
                    array($attribute=>$value, $this->primary=>$this->id()));
    }

    /**
    * Format the values when the type of the attribute is multiple.
    */
    public function formatMultipleValues($values) {
        foreach($this->info->attributes->attribute as $item) {
            $name = (string)$item->name;
            $type = (string)$item->type;
            if ($type=='multiple-object' && isset($values[$name]) && is_array($values[$name])) {
                foreach ($values[$name] as $key=>$arrayObject) {
                    $values[$name][$key]['refMultiple'] = $name.'-'.$key;
                }
            }
        }
        return $values;
    }

    /**
    * Insert the values related to an object.
    */
    public function insertMultiple($values, $options=array()) {
        foreach($this->info->attributes->attribute as $item) {
            $name = (string)$item->name;
            $type = (string)$item->type;
            switch ($type) {
                case 'multiple-object':
                    $refObject = (string)$item->refObject;
                    $lnkAttribute = (string)$item->lnkAttribute;
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
                case 'multiple-autocomplete':
                    $refObject = (string)$item->refObject;
                    $lnkObject = (string)$item->lnkObject;
                    $refAttribute = (string)$item->refAttribute;
                    $refObjectIns = new $refObject();
                    $lnkObjectIns = new $lnkObject();
                    $complete = (isset($options['complete'])) ? $options['complete'] : true;
                    if ($complete) {
                        Db::execute('DELETE FROM '.Db::prefixTable($lnkObjectIns->className).' WHERE '.$this->primary.'="'.$this->id().'"');
                    }
                    if (isset($values[$name]) && $values[$name]!='') {
                        $autocompleteItems = explode(',', $values[$name]);
                        foreach ($autocompleteItems as $autocompleteItem) {
                            $autocompleteItem = trim($autocompleteItem);
                            if ($autocompleteItem!='') {
                                //Check if it already exists
                                $autocompleteObject = $refObjectIns->readFirstObject(array('where'=>'BINARY '.$refAttribute.'="'.$autocompleteItem.'"'));
                                if ($autocompleteObject->id()=='') {
                                    $autocompleteObject = new $refObject();
                                    $autocompleteObject->insert(array($refAttribute=>$autocompleteItem));
                                }
                                //Check if the link exists and make it
                                $lnkObjectIns = $lnkObjectIns->readFirstObject(array('where'=>$this->primary.'='.$this->id().' AND '.$refObjectIns->primary.'='.$autocompleteObject->id()));
                                if ($lnkObjectIns->get($this->primary)=='') {
                                    $lnkObjectIns->insert(array($this->primary=>$this->id(),
                                                                $refObjectIns->primary=>$autocompleteObject->id()),
                                                                array('simpleQuery'=>true));
                                }
                            }
                        }
                    }
                break;
                case 'multiple-checkbox':
                case 'multiple-select':
                    $complete = (isset($options['complete'])) ? $options['complete'] : true;
                    $refObject = (string)$item->refObject;
                    $refObjectIns = new $refObject();
                    if ((string)$item->lnkObject != '') {
                        $lnkObject = (string)$item->lnkObject;
                        $lnkObjectIns = new $lnkObject();
                        if ($complete) {
                            Db::execute('DELETE FROM '.Db::prefixTable($lnkObjectIns->className).' WHERE '.$this->primary.'="'.$this->id().'"');
                        }
                        if (isset($values[$name]) && is_array($values[$name])) {
                            foreach ($values[$name] as $key=>$itemMultiple) {
                                //If it's an object
                                if (is_object($itemMultiple)) {
                                    $objectExists = $lnkObjectIns->readFirstObject(array('where'=>$this->primary.'="'.$this->id().'" AND '.$refObjectIns->primary.'="'.$itemMultiple->id().'"'));
                                    if ($objectExists->get($this->primary)=='') {
                                        $lnkObjectIns->insert(array($this->primary=>$this->id(),
                                                                $refObjectIns->primary=>$itemMultiple->id()),
                                                                array('simpleQuery'=>true));
                                    }
                                } else if (is_array($itemMultiple)) {
                                    //If it's an array
                                    $refObjectNew = new $refObject();
                                    $refObjectNew->insert($itemMultiple);
                                    $lnkObjectIns->insert(array($this->primary=>$this->id(),
                                                            $refObjectIns->primary=>$refObjectNew->id()),
                                                            array('simpleQuery'=>true));
                                } else if ($itemMultiple=='on') {
                                    //If it's just a "on" checkbox
                                    $lnkObjectExists = new $lnkObject();
                                    $lnkObjectExists = $lnkObjectIns->readFirstObject(array('where'=>$this->primary.'="'.$this->id().'" AND '.$refObjectIns->primary.'="'.$key.'"'));
                                    if ($lnkObjectExists->get($this->primary)=='') {
                                        $lnkObjectNew = new $lnkObject();
                                        $lnkObjectNew->insert(array($this->primary=>$this->id(), $refObjectIns->primary=>$key), array('simpleQuery'=>true));
                                    }
                                } else {
                                    // If it's just an id from a multiple select
                                    $lnkObjectExists = new $lnkObject();
                                    $lnkObjectExists = $lnkObjectIns->readFirstObject(array('where'=>$this->primary.'="'.$this->id().'" AND '.$refObjectIns->primary.'="'.$itemMultiple.'"'));
                                    if ($lnkObjectExists->get($this->primary)=='') {
                                        $lnkObjectNew = new $lnkObject();
                                        $lnkObjectNew->insert(array($this->primary=>$this->id(), $refObjectIns->primary=>$itemMultiple), array('simpleQuery'=>true));
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($values[$name]) && is_array($values[$name])) {
                            if ($complete) {
                                Db::execute('UPDATE '.Db::prefixTable($refObject).' SET '.$this->primary.'=NULL WHERE '.$this->primary.'="'.$this->id().'"');
                            }
                            foreach ($values[$name] as $key=>$itemMultiple) {
                                if ($itemMultiple=='on') {
                                    $refObjectUpdate = new $refObject();
                                    $refObjectUpdate = $refObjectUpdate->readObject($key);
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
    public function delete(){
        if ($this->id()!='') {
            $this->deleteFiles();
            $query = 'DELETE FROM '.$this->tableName.'
                        WHERE '.$this->primary.'="'.$this->id().'"';
            Db::execute($query);
            $onDelete = (string)$this->info->info->sql->onDelete;
            if ($onDelete!='') {
                $onDeleteFields = explode(',', $onDelete);
                foreach ($onDeleteFields as $onDeleteField) {
                    $onDeleteObject = new $onDeleteField;
                    $listObjects = $onDeleteObject->readListObject(array('where'=>$this->primary.'="'.$this->id().'"'));
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
    public function fieldPoints() {
        $points = $this->info->xpath('//type[.="point"]/parent::*');
        $fields = '';
        foreach($points as $point) {
            $pointName = (string)$point->name;
            $fields .= ', CONCAT(X('.$pointName.'),":",Y('.$pointName.')) AS '.$pointName;
        }
        return $fields;
    }

    /**
    * Update the order of a list of objects.
    */
    public function updateOrder($values){
        $idObject = (string)$this->primary;
        $i=1;
        foreach($values as $value) {
            $query = 'UPDATE '.$this->tableName.'
                        SET ord='.$i.'
                        WHERE '.$idObject.'="'.$value.'"';
            Db::execute($query);
            $i++;
        }
    }

    /**
    * Creates the table using the information in the XML file.
    */
    public function createTable($rewrite=false) {
        if ($rewrite) {
            $query = 'DROP TABLE IF EXISTS `'.DB_PREFIX.$this->className.'`';
            Db::execute($query);
        }
        $existsQuery = 'SHOW TABLES LIKE "'.DB_PREFIX.$this->className.'"';
        $exists = Db::returnAll($existsQuery);
        if (count($exists)==0) {
            $query = 'CREATE TABLE `'.DB_PREFIX.$this->className.'` (';
            $query .= ($this->info->info->sql->order == 'true') ? '`ord` int(10) unsigned DEFAULT NULL,' : '';
            $query .= ($this->info->info->sql->created == 'true') ? '`created` DATETIME DEFAULT NULL,' : '';
            $query .= ($this->info->info->sql->modified == 'true') ? '`modified` DATETIME DEFAULT NULL,' : '';
            $queryFields = '';
            foreach($this->getAttributes() as $item) {
                $queryFields .= Db_ObjectType::createTableSql($item);
            }
            $engine = ((string)$this->info->info->sql->engine != '') ? (string)$this->info->info->sql->engine : 'MyISAM';
            $query .= substr($queryFields, 0, -1).') ENGINE='.$engine.' COLLATE utf8_unicode_ci;';
            Db::execute($query);
            $this->createTableIndexes();
            // Create related tables
            if ((string)$this->info->info->sql->onCreate!='') {
                $relatedTables = explode(',',(string)$this->info->info->sql->onCreate);
                foreach ($relatedTables as $relatedTable) {
                    $relatedTable = trim($relatedTable);
                    $object = new $relatedTable();
                    $object->createTable($rewrite);
                }
            }
        }
    }

    /**
    * Creates the table indexes defined in the class XML file.
    */
    public function createTableIndexes($rewrite=false) {
        if (isset($this->info->indexes)) {
            foreach($this->info->indexes->index as $item) {
                $name = (string)$item->name;
                $type = (string)$item->type;
                $fields = (string)$item->fields;
                $lang = (string)$item->lang;
                if ($lang=='true') {
                    foreach (Lang::langs() as $lang) {
                        $name = (string)$item->name.'_'.$lang;
                        $query = 'SHOW INDEX FROM '.$this->tableName.' WHERE KEY_NAME="'.$name.'"';
                        if (count(Db::returnAll($query))==0) {
                            $query = 'CREATE '.$type.' INDEX `'.$name.'` ON '.$this->tableName.' ('.$name.')';
                            Db::execute($query);
                        }
                    }
                } else {
                    $query = 'SHOW INDEX FROM '.$this->tableName.' WHERE KEY_NAME="'.$name.'"';
                    if (count(Db::returnAll($query))==0) {
                        $query = 'CREATE '.$type.' INDEX `'.$name.'` ON '.$this->tableName.' ('.$fields.')';
                        Db::execute($query);
                    }
                }
            }
        }
    }

    /**
    * Creates a SET string used in the insertion and modification of the values in the DB.
    */
    public function createSet($values, $complete=true) {
        $query = '';
        $setValues = array();
        if (isset($values['ord']) && $values['ord']!='') {
            $setValues['ord'] = $values['ord'];
            $query .= '`ord` = :ord, ';
        }
        foreach($this->info->attributes->attribute as $item) {
            $name = (string)$item->name;
            $type = (string)$item->type;
            switch ($type) {
                default:
                    if ((string)$item->lang == 'true') {
                        foreach (Lang::langs() as $lang) {
                            $nameLang = $name.'_'.$lang;
                            if (isset($values[$nameLang])) {
                                $setValues[$nameLang] = $values[$nameLang];
                                $query .= '`'.$nameLang.'` = :'.$nameLang.', ';
                            }
                        }
                    } else {
                        if (isset($values[$name])) {
                            $setValues[$name] = $values[$name];
                            $query .= '`'.$name.'` = :'.$name.', ';
                        }
                    }
                break;
                case 'id-char32':
                    if ($this->id()=='' && (!isset($values[$name]) || $values[$name]=='')) {
                        $idMd5 = md5(microtime()*rand()*rand());
                        $setValues[$name] = $idMd5;
                        $query .= '`'.$name.'` = :'.$name.', ';
                        $this->setId($idMd5);
                    }
                break;
                case 'id-varchar':
                    if (isset($values[$name])) {
                        $setValues[$name] = Text::simpleUrl($values[$name], '');
                        $query .= '`'.$name.'` = :'.$name.', ';
                        $this->setId($setValues[$name]);
                    }
                break;
                case 'password':
                    if (isset($values[$name])) {
                        $password = md5($values[$name]);
                        $setValues[$name] = $password;
                        $query .= '`'.$name.'` = :'.$name.', ';
                    }
                    if (isset($values[$name.'_new']) && $values[$name.'_new']!='') {
                        $password = md5($values[$name.'_new']);
                        $setValues[$name] = $password;
                        $query .= '`'.$name.'` = :'.$name.'", ';
                    }
                break;
                case 'hidden-url':
                    if ((string)$item->lang == 'true') {
                        foreach (Lang::langs() as $lang) {
                            $textUrl = (string)$item->refAttribute.'_'.$lang;
                            $nameLang = $name.'_'.$lang;
                            if (isset($values[$textUrl])) {
                                $setValues[$nameLang] = Text::simple($values[$textUrl]);
                                $query .= '`'.$nameLang.'` = :'.$nameLang.', ';
                            }
                        }
                    } else {
                        $textUrl = (string)$item->refAttribute;
                        if (isset($values[$textUrl])) {
                            $setValues[$name] = Text::simple($values[$textUrl]);
                            $query .= '`'.$name.'` = :'.$name.', ';
                        }
                    }
                break;
                case 'hidden-user':
                    if (isset($values[$name.'_force'])) {
                        $setValues[$name] = $values[$name.'_force'];
                        $query .= $name.' = :'.$name.', ';
                    } elseif (!isset($values[$name]) || $values[$name]=='') {
                        $userLogged = User_Login::getInstance();
                        $setValues[$name] = $userLogged->id();
                        $query .= $name.' = :'.$name.', ';
                    }
                break;
                case 'point':
                    $pointLat = (isset($values[$name.'_lat'])) ? floatval($values[$name.'_lat']) : 0;
                    $pointLng = (isset($values[$name.'_lng'])) ? floatval($values[$name.'_lng']) : 0;
                    $query .= (isset($values[$name.'_lat']) && isset($values[$name.'_lng'])) ? '`'.$name.'`=POINT('.$pointLat.', '.$pointLng.'), ' : '';
                break;
                case 'text-code':
                    $query .= (isset($values[$name]) && $values[$name]!="") ? '`'.$name.'`="'.Text::simpleCode($values[$name]).'", ' : '';
                break;
                case 'text-double':
                    $query .= (isset($values[$name]) && $values[$name]!="") ? '`'.$name.'`="'.floatval($values[$name]).'", ' : '';
                break;
                case 'text-integer':
                    $query .= (isset($values[$name]) && $values[$name]!="") ? '`'.$name.'`="'.intval($values[$name]).'", ' : '';
                break;
                case 'date':
                case 'date-complete':
                case 'date-hour':
                case 'date-text':
                case 'date-checkbox':
                    if ($type == 'date-checkbox') {
                        if ($complete) {
                            $nameCheckbox = $name.'_checkbox';
                            $values[$nameCheckbox] = (isset($values[$nameCheckbox])) ? $values[$nameCheckbox] : 0;
                            $values[$nameCheckbox] = ($values[$nameCheckbox]==="on") ? 1 : $values[$nameCheckbox];
                            if ($values[$nameCheckbox]=='1') {
                                if (isset($values[$name]) && !isset($values[$name.'yea'])) {
                                    $query .= '`'.$name.'`="'.$values[$name].'", ' ;
                                } else {
                                    $yea = isset($values[$name.'yea']) ? str_pad(intval($values[$name.'yea']), 2, "0", STR_PAD_LEFT) : 0;
                                    $mon = isset($values[$name.'mon']) ? str_pad(intval($values[$name.'mon']), 2, "0", STR_PAD_LEFT) : 0;
                                    $day = isset($values[$name.'day']) ? str_pad(intval($values[$name.'day']), 2, "0", STR_PAD_LEFT) : 0;
                                    $hou = isset($values[$name.'hou']) ? str_pad(intval($values[$name.'hou']), 2, "0", STR_PAD_LEFT) : 0;
                                    $min = isset($values[$name.'min']) ? str_pad(intval($values[$name.'min']), 2, "0", STR_PAD_LEFT) : 0;
                                    $date = $yea.'-'.$mon.'-'.$day.' '.$hou.':'.$min.':00';
                                    $query .= isset($values[$name.'yea']) ? '`'.$name.'`="'.$date.'", ' : '';
                                }
                            } else {
                                $query .= '`'.$name.'`=NULL, ' ;
                            }
                        }
                    } else {
                        if (isset($values[$name]) && !isset($values[$name.'yea'])) {
                            $query .= '`'.$name.'`="'.$values[$name].'", ' ;
                        } else {
                            $yea = isset($values[$name.'yea']) ? str_pad(intval($values[$name.'yea']), 2, "0", STR_PAD_LEFT) : 0;
                            $mon = isset($values[$name.'mon']) ? str_pad(intval($values[$name.'mon']), 2, "0", STR_PAD_LEFT) : 0;
                            $day = isset($values[$name.'day']) ? str_pad(intval($values[$name.'day']), 2, "0", STR_PAD_LEFT) : 0;
                            $hou = isset($values[$name.'hou']) ? str_pad(intval($values[$name.'hou']), 2, "0", STR_PAD_LEFT) : 0;
                            $min = isset($values[$name.'min']) ? str_pad(intval($values[$name.'min']), 2, "0", STR_PAD_LEFT) : 0;
                            $date = $yea.'-'.$mon.'-'.$day.' '.$hou.':'.$min.':00';
                            $query .= (isset($values[$name.'yea']) || isset($values[$name.'hou'])) ? '`'.$name.'`="'.$date.'", ' : '';
                        }
                    }
                break;
                case 'date-text':
                    if (isset($values[$name])) {
                        $valueDate = $values[$name];
                        $valueDateInfo = explode('-', $valueDate);
                        if (isset($valueDateInfo[2])) {
                            $valueDate = intval($valueDateInfo[2]).'-'.intval($valueDateInfo[1]).'-'.intval($valueDateInfo[0]);
                        } else {
                            $valueDate = '0000-00-00';
                        }
                        $query .= '`'.$name.'`="'.$valueDate.'", ';
                    }
                break;
                case 'checkbox':
                    if ($complete) {
                        $values[$name] = (isset($values[$name])) ? $values[$name] : 0;
                        $values[$name] = ($values[$name]==="on") ? 1 : $values[$name];
                        $query .= isset($values[$name]) ? '`'.$name.'`="'.$values[$name].'", ' : '`'.$name.'`=NULL, ';
                    }
                break;
                case 'select-checkbox':
                    if ($complete) {
                        $nameCheckbox = $name.'_checkbox';
                        $values[$nameCheckbox] = (isset($values[$nameCheckbox])) ? $values[$nameCheckbox] : 0;
                        $values[$nameCheckbox] = ($values[$nameCheckbox]==="on") ? 1 : $values[$nameCheckbox];
                        $query .= ($values[$nameCheckbox]=='1' && isset($values[$name])) ? '`'.$name.'`="'.$values[$name].'", ' : '`'.$name.'`=NULL, ';
                    }
                break;
                case 'id-autoincrement':
                case 'file':
                case 'multiple-object':
                case 'multiple-autocomplete':
                case 'multiple-checkbox':
                case 'multiple-select':
                break;
            }
        }
        $query = ($query!='') ? substr($query, 0, -2) : $query;
        return array('query'=>$query, 'setValues'=>$setValues);
    }

    /**
    * Upload the files of an object according the its attributes.
    */
    public function uploadFiles($values=array()) {
        $fields = $this->info->xpath('//type[.="file"]/parent::*');
        foreach($fields as $field) {
            $fieldName = (string)$field->name;
            // Upload from the FILES array
            if (isset($values['refMultiple']) && $values['refMultiple']!='') {
                $fieldNameMultiple = $values['refMultiple'].'-'.$fieldName;
                // Case multiple
                if (isset($_FILES[$fieldNameMultiple]) && isset($_FILES[$fieldNameMultiple]['tmp_name']) && $_FILES[$fieldNameMultiple]['tmp_name']!='') {
                    if ((string)$field->mode == 'image') {
                        $fileTmp = $_FILES[$fieldNameMultiple]['tmp_name'];
                        $fileSave = Text::simpleUrlFileBase($this->id().'_'.$fieldName);
                        if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                            $this->modifySimple($fieldName, $fileSave);
                        }
                        unset($_FILES[$fieldNameMultiple]);
                    } else {
                        $fileTmp = $_FILES[$fieldNameMultiple]['tmp_name'];
                        $fileSave = $this->id().'_'.Text::simpleUrlFile($_FILES[$fieldNameMultiple]['name']);
                        if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                            $this->modifySimple($fieldName, $fileSave);
                        }
                        unset($_FILES[$fieldNameMultiple]);
                    }
                }
            }
            // Case single
            if (isset($_FILES[$fieldName]) && isset($_FILES[$fieldName]['tmp_name']) && $_FILES[$fieldName]['tmp_name']!='') {
                if ((string)$field->mode == 'adaptable') {
                    // Image and file
                    $fileTmp = $_FILES[$fieldName]['tmp_name'];
                    $fileName = $_FILES[$fieldName]['name'];
                    switch (File::fileExtension($fileName)) {
                        default:
                            $fileSave = $this->id().'_'.Text::simpleUrlFile($_FILES[$fieldName]['name']);
                            if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                                $this->modifySimple($fieldName, $fileSave);
                            }
                        break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                            $fileSave = Text::simpleUrlFileBase($this->id().'_'.$fieldName);
                            if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                                $this->modifySimple($fieldName, $fileSave);
                            }
                        break;
                    }
                    unset($_FILES[$fieldName]);
                } elseif ((string)$field->mode == 'image') {
                    // Image only
                    $fileTmp = $_FILES[$fieldName]['tmp_name'];
                    $fileSave = Text::simpleUrlFileBase($this->id().'_'.$fieldName);
                    if (Image_File::saveImageUrl($fileTmp, $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                    unset($_FILES[$fieldName]);
                } else {
                    // File only
                    $fileTmp = $_FILES[$fieldName]['tmp_name'];
                    $fileSave = $this->id().'_'.Text::simpleUrlFile($_FILES[$fieldName]['name']);
                    if (File::uploadUrl($fileTmp, $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                    unset($_FILES[$fieldName]);
                }
            }
            // Upload from an URL
            if (isset($values[$fieldName]) && is_string($values[$fieldName])) {
                if ((string)$field->mode == 'image') {
                    $fileSave = Text::simpleUrlFileBase($this->id().'_'.$fieldName);
                    if (Image_File::saveImageUrl($values[$fieldName], $this->className, $fileSave)) {
                        $this->modifySimple($fieldName, $fileSave);
                    }
                } else {
                    $fileSave = $this->id().'_'.$fieldName.'.'.File::urlExtension($values[$fieldName]);
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
    public function deleteFiles() {
        foreach($this->info->attributes->attribute as $item) {
            if ((string)$item->type == "file") {
                $name = Text::simpleUrlFileBase((string)$item->name);
                if ((string)$item->mode == 'image') {
                    Image_File::deleteImage($this->className, $this->id().'-'.$name);
                } else {
                    $file = STOCK_FILE.$this->className.'Files/'.$this->get($name);
                    @unlink($file);
                }
            }
        }
    }

}
?>