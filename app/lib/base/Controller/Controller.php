<?php
/**
* @class Controller
*
* This is the "controller" component of the MVC pattern used by Asterion.
* All of the controllers for the content objects extend from this class.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
abstract class Controller{

    /**
    * The general constructor for the controllers.
    * $GET : Array with the loaded $_GET values.
    * $POST : Array with the loaded $_POST values.
    * $FILES : Array with the loaded $_FILES values.
    */
    public function __construct($GET, $POST, $FILES) {
        $this->type = isset($GET['type']) ? $GET['type'] : '';
        $this->action = isset($GET['action']) ? $GET['action'] : 'list';
        $this->id = isset($GET['id']) ? $GET['id'] : '';
        $this->extraId = isset($GET['extraId']) ? $GET['extraId'] : '';
        $this->addId = isset($GET['addId']) ? $GET['addId'] : '';
        $this->params = isset($GET) ? $GET : array();
        $this->values = isset($POST) ? $POST : array();
        $this->files = isset($FILES) ? $FILES : array();
        $this->login = User_Login::getInstance();
    }
    
    /**
    * Function to get the title for a page.
    * By default it uses the title defined in the Parameters.
    */
    public function getTitle() {
        return (isset($this->titlePage)) ? $this->titlePage.' - '.Params::param('metainfo-titlePage') : Params::param('metainfo-titlePage');
    }   

    /**
    * Function to get the extra header tags for a page.
    * It can be used to load extra CSS or JS files.
    */
    public function getHeader() {
        return (isset($this->header)) ? $this->header : '';
    }

    /**
    * Function to get the meta-description for a page.
    * By default it uses the meta-description defined in the Parameters.
    */
    public function getMetaDescription() {
        return (isset($this->metaDescription) && $this->metaDescription!='') ? $this->metaDescription : Params::param('metainfo-metaDescription');
    }

    /**
    * Function to get the meta-keywords for a page.
    * By default it uses the keywords defined in the Parameters.
    */
    public function getMetaKeywords() {
        return (isset($this->metaKeywords)) ? $this->metaKeywords : Params::param('metainfo-metaKeywords');
    }

    /**
    * Function to get the meta-image for a page.
    * By default it uses the LOGO defined in the configuration file.
    */
    public function getMetaImage() {
        $image = (isset($this->metaImage) && $this->metaImage!='') ? $this->metaImage : LOGO;
        $imageFile = str_replace(BASE_URL, BASE_FILE, $image);
        if (is_file($imageFile)) {
            $imageSize = getimagesize($imageFile);
            return '<meta property="og:image" content="'.$image.'" />
                    <meta property="og:image:width" content="'.$imageSize[0].'" />
                    <meta property="og:image:height" content="'.$imageSize[1].'" />';
        }
    }

    /**
    * Function to get the url address for a page.
    * A common use is the canonical URL of the current page.
    */
    public function getMetaUrl() {
        return (isset($this->metaUrl) && $this->metaUrl!='') ? $this->metaUrl : url('');
    }

    /**
    * Function to get the mode to render a page.
    * By default it uses the public method.
    * The render goes on the main index.php file.
    */
    public function getMode() {
        return (isset($this->mode)) ? $this->mode : 'public';
    }

    /**
    * Main function of the controller.
    * It works as a huge switch that uses the $action attribute defined in the URL.
    * By default this actions are built for the BackEnd since we usually do not modify
    * the objects in the FrontEnd. However for those situations we must override this
    * function in the child controller.
    */
    public function controlActions(){
        // Check if the tables are created.
        $this->mode = 'admin';
        $this->object = new $this->type();
        $this->titlePage = __((string)$this->object->info->info->form->title);
        $this->layout = (string)$this->object->info->info->form->layout;
        $this->menuInside = $this->menuInside();
        $ui = new NavigationAdmin_Ui($this);
        switch ($this->action) {
            default:
                header('Location: '.url($this->type.'/listAdmin', true));
                exit();
            break;
            case 'listAdmin':
                /**
                * This is the main action for the BackEnd. If we are in DEBUG mode
                * it will create the table automatically.
                */
                $this->checkLoginAdmin();
                $this->content = $this->listAdmin();
                return $ui->render();
            break;
            case 'insertView':
                /**
                * This is the action that shows the form to insert a record in the BackEnd.
                */
                $this->checkLoginAdmin();
                $this->content = $this->insertView();
                return $ui->render();
            break;
            case 'insert':
                /**
                * This is the action that inserts a record in the BackEnd.
                * If the insertion is successful it shows a form to check the record,
                * if not it creates a form with the errors to correct.
                */
                $this->checkLoginAdmin();
                $insert = $this->insert();
                if ($insert['success']=='1') {
                    header('Location: '.url($this->type.'/insertCheck/'.$insert['id'], true));
                    exit();
                } else {
                    $this->messageError = __('errorsForm');
                    $this->content = $insert['html'];
                    return $ui->render();
                }
            break;
            case 'modifyView':
            case 'modifyViewCheck':
            case 'insertCheck':
                /**
                * This is the action that shows the form to check a record insertion.
                */
                $this->checkLoginAdmin();
                $this->message = ($this->action=='insertCheck' || $this->action=='modifyViewCheck') ? __('savedForm') : '';
                $this->content = $this->modifyView();
                return $ui->render();
            break;
            case 'modifyViewNested':
                /**
                * This is the action that shows the form to modify a record.
                */
                $this->checkLoginAdmin();
                $this->object = $this->object->readObject($this->id);
                $uiObjectName = $this->type.'_Ui';
                $uiObject = new $uiObjectName($this->object);
                $values = array_merge($this->object->valuesArray(), $this->values);
                $this->content = $uiObject->renderForm(array_merge(
                                                        array('values'=>$values,
                                                                'action'=>url($this->type.'/modifyNested', true),
                                                                'class'=>'formAdmin formAdminModify',
                                                                'nested'=>true),
                                                        array()));
                return $ui->render();
            break;
            case 'modify':
            case 'modifyNested':
                /**
                * This is the action that updates a record when updating it.
                */
                $this->checkLoginAdmin();
                $nested = ($this->action == 'modifyNested') ? true : false;
                $modify = $this->modify($nested);
                if ($modify['success']=='1') {
                    if (isset($this->values['submit-saveCheck'])) {
                        header('Location: '.url($this->type.'/modifyViewCheck/'.$modify['id'], true));
                    } else {
                        header('Location: '.url($this->type.'/listAdmin', true));
                    }
                    exit();
                } else {
                    $this->messageError = __('errorsForm');
                    $this->content = $modify['html'];
                    return $ui->render();
                }
            break;
            case 'delete':
                /**
                * This is the action that deletes a record.
                */
                $this->checkLoginAdmin();
                if ($this->id != '') {
                    $type = new $this->type();
                    $object = $type->readObject($this->id);
                    $object->delete();
                }
                header('Location: '.url($this->type.'/listAdmin', true));
                exit();
            break;
            case 'sortSave':
                /**
                * This is the action that saves the order of a list of records.
                * It is used when sorting using the BackEnd.
                */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                $object = new $this->type();
                $newOrder = (isset($this->values['newOrder'])) ? $this->values['newOrder'] : array();
                $object->updateOrder($newOrder);
            break;
            case 'sortList':
                /**
                * This is the action that changes the order of the list.
                */
                $this->checkLoginAdmin();
                $object = new $this->type();
                $info = explode('_', $this->id);
                if (isset($info[1]) && $object->attributeInfo($info[1])!='') {
                    $orderType = ($info[0]=='asc') ? 'asc' : 'des';
                    Session::set('ord_'.$this->type, $orderType.'_'.$info[1]);
                }
                header('Location: '.url($this->type, true));
                exit();
            break;
            case 'addSimple':
                /**
                * This is the action that adds a simple record.
                */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                $formObject = $this->type.'_Form';
                $form = new $formObject();
                return $form->createFormFieldMultiple();
            break;
            case 'multiple-delete':
                /**
                * This is the action that deletes multiple records at once.
                */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if (isset($this->values['list-ids'])) {
                    $type = new $this->type();
                    foreach ($this->values['list-ids'] as $id) {
                        $object = $type->readObject($id);
                        $object->delete();
                    }
                }
            break;
            case 'multiple-activate':
            case 'multiple-deactivate':
                /**
                * This is the action that activates or deactivates multiple records at once.
                * It just works on records that have an attribute named "active",
                */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if (isset($this->values['list-ids'])) {
                    $primary = (string)$this->object->info->info->sql->primary;
                    $where = '';
                    foreach ($this->values['list-ids'] as $id) {
                        $where .= $primary.'="'.$id.'" OR ';
                    }
                    $where = substr($where, 0, -4);
                    $active = ($this->action == 'multiple-activate') ? '1' : '0';
                    $query = 'UPDATE '.Db::prefixTable($this->type).' SET active="'.$active.'" WHERE '.$where;
                    Db::execute($query);
                }
            break;
            case 'autocomplete':
                /**
                * This is the action that returns a json string with the records that match a search string.
                * It is used for the autocomplete text input.
                */
                $this->mode = 'json';
                $autocomplete = (isset($_GET['term'])) ? $_GET['term'] : '';
                if ($autocomplete!='') {
                    $where = '';
                    $concat = '';
                    $items = explode('_', $this->id);
                    foreach ($items as $itemIns) {
                        $item = $this->object->attributeInfo($itemIns);
                        $name = (string)$item->name;
                        if (is_object($item) && $name!='') {
                            $concat .= $name.'," ",';
                            $where .= $name.' LIKE "%'.$autocomplete.'%" OR ';
                        }
                    }
                    $where = substr($where, 0, -4);
                    $concat = 'CONCAT('.substr($concat, 0, -5).')';
                    if ($where!='') {
                        $query = 'SELECT '.(string)$this->object->info->info->sql->primary.' as idItem, 
                                '.$concat.' as infoItem
                                FROM '.Db::prefixTable($this->type).'
                                WHERE '.$where.'
                                ORDER BY '.$name.' LIMIT 20';
                        $results = array();
                        $resultsAll = Db::returnAll($query);
                        foreach ($resultsAll as $result) {
                            $resultsIns = array();
                            $resultsIns['id'] = $result['idItem'];
                            $resultsIns['value'] = $result['infoItem'];
                            $resultsIns['label'] = $result['infoItem'];
                            array_push($results, $resultsIns);
                        }
                        return json_encode($results);                        
                    }
                }
            break;
            case 'search':
                /**
                * This is the action that does the default "search" on a content object.
                */
                $this->checkLoginAdmin();
                if ($this->id != '') {
                    $this->content = $this->listAdmin();
                    return $ui->render();
                } else {
                    if (isset($this->values['search']) && $this->values['search']!='') {
                        $searchString = urlencode(html_entity_decode($this->values['search']));
                        header('Location: '.url($this->type.'/search/'.$searchString, true));
                    } else {
                        header('Location: '.url($this->type.'/listAdmin', true));
                    }    
                }
            break;
            case 'export-json':
                /**
                * This is the action that exports the complete list of objects in JSON format.
                */
                $this->mode = 'ajax';
                $query = 'SELECT * FROM '.Db::prefixTable($this->type);
                $items = Db::returnAll($query);
                $file = $this->type.'.json';
                $options = array('content'=>json_encode($items), 'contentType'=>'application/json');
                File::download($file, $options);
                return '';
            break;
        }
    }

    /**
    * Render the list of the items in the administration area.
    */
    public function listAdmin() {
        if ((string)$this->object->info->info->form->group!='') {
            return $this->listGroupAdmin();
        }
        $search = $this->object->infoSearch();
        $searchQuery = $this->object->infoSearchQuery();
        $searchQueryCount = $this->object->infoSearchQueryCount();
        $searchValue = urldecode($this->id);
        $sortableListClass = ($this->object->hasOrd()) ? 'sortableList' : '';
        $ordObject = explode('_', Session::get('ord_'.$this->type));
        $ordObjectType = (isset($ordObject[0]) && $ordObject[0]=='asc') ? 'ASC' : 'DESC';
        $options['order'] = $this->orderField();
        if (isset($ordObject[1])) {
            $orderInfo = $this->object->attributeInfo($ordObject[1]);
            $orderInfoItem = (is_object($orderInfo) && (string)$orderInfo->lang == "true") ? $ordObject[1].'_'.Lang::active() : $ordObject[1];
            $options['order'] = $orderInfoItem.' '.$ordObjectType;
        }
        $options['results'] = (int)$this->object->info->info->form->pager;
        $options['where'] = ($search!='' && $searchValue!='') ? str_replace('#SEARCH', $searchValue, $search) : '';
        $options['query'] = ($searchQuery!='' && $searchValue!='') ? str_replace('#SEARCH', $searchValue, $searchQuery) : '';
        $options['queryCount'] = ($searchQueryCount!='' && $searchValue!='') ? str_replace('#SEARCH', $searchValue, $searchQueryCount) : '';
        $list = new ListObjects($this->type, $options);
        $multipleChoice = (count((array)$this->object->info->info->form->multipleActions->action) > 0);
        $controlsTop = $this->multipleActionsControl().$this->orderControl();
        $controlsTop = ($controlsTop != '') ? '<div class="controlsTop">'.$controlsTop.'</div>' : '';
        return $this->searchForm().'
                '.$controlsTop.'
                <div class="listAdmin listAdmin'.$this->type.' '.$sortableListClass.'" rel="'.url($this->type.'/sortSave/', true).'">
                    '.$list->showListPager(array('function'=>'Admin',
                                                'message'=>'<div class="message">'.__('noItems').'</div>'),
                                            array('userType'=>$this->login->get('type'), 'multipleChoice'=>$multipleChoice)).'
                </div>';
    }

    /**
    * Render the list of the items when is the case of a group.
    */
    public function listGroupAdmin() {
        $group = (string)$this->object->info->info->form->group;
        $items = $this->object->getValues($group, true);
        $listItems = '';
        foreach ($items as $key=>$item) {
            $sortableListClass = ($this->object->hasOrd()) ? 'sortableList' : '';
            $list = new ListObjects($this->type, array('where'=>$group.'="'.$key.'"',
                                                        'function'=>'Admin',
                                                        'order'=>$this->orderField()));
            $listItems .= '<div class="lineAdminBlock">
                                <div class="lineAdminTitle">'.$item.'</div>
                                <div class="lineAdminItems">
                                    <div class="listAdmin '.$sortableListClass.'" rel="'.url($this->type.'/sortSave/', true).'">
                                        '.$list->showList(array('function'=>'Admin',
                                                                'message'=>'<div class="message">'.__('noItems').'</div>'),
                                                            array('userType'=>$this->login->get('type'))).'
                                    </div>
                                </div>
                            </div>';
        }
        return '<div class="lineAdminBlockWrapper">'.$listItems.'</div>';
    }

    /**
    * Check for the order field.
    */
    public function orderField() {
        $orderAttribute = (string)$this->object->info->info->form->orderBy;
        if ($orderAttribute!='') {
            $orderAttribute = explode(',', $orderAttribute);
            $orderAttribute = explode(' ', $orderAttribute[0]);
            $orderType = (isset($orderAttribute[1]) && $orderAttribute[1]=='DESC') ? 'DESC' : 'ASC';
            $orderAttribute = $orderAttribute[0];
            $orderInfo = $this->object->attributeInfo($orderAttribute);
            return (is_object($orderInfo) && (string)$orderInfo->lang == "true") ? $orderAttribute.'_'.Lang::active().' '.$orderType : $orderAttribute.' '.$orderType;
        }
    }

    /**
    * Render the order control.
    */
    public function orderControl() {
        $orderField = (string)$this->object->info->info->form->orderBy;
        $orderItems = explode(',', $orderField);
        if (count($orderItems)>1 && $orderField != '') {
            $options = array();
            $selectedItem = Session::get('ord_'.$this->type);
            foreach ($orderItems as $orderItem) {
                $infoOrderItem = explode(' ', trim($orderItem));
                $options['asc_'.$infoOrderItem[0]] = __($infoOrderItem[0]);
                $options['des_'.$infoOrderItem[0]] = __($infoOrderItem[0]).' ('.__('reverse').')';
            }
            return '<div class="orderActions" rel="'.url($this->type.'/sortList/', true).'">
                        <div class="orderActionsIns">
                            '.FormField::create('select', array('label'=>__('orderBy'), 'name'=>'orderList', 'value'=>$options, 'selected'=>$selectedItem)).'
                        </div>
                    </div>';
        }
    }

    /**
    * Render the multiple actions control.
    */
    public function multipleActionsControl() {
        $multipleActions = (array)$this->object->info->info->form->multipleActions->action;
        if (count($multipleActions) > 0) {
            $multipleActionsOptions = '';
            foreach ($multipleActions as $multipleAction) {
                $linkMultiple = url($this->type.'/multiple-'.$multipleAction, true);
                $multipleActionsOptions .= '<div class="multipleAction multipleOption" rel="'.$linkMultiple.'">
                                                '.__($multipleAction.'Selected').'
                                            </div>';    
            }
            return '<div class="multipleActions">
                        <div class="multipleAction multipleActionCheckAll">
                            '.FormField_Checkbox::create(array('name'=>'checkboxList')).'
                        </div>
                        '.$multipleActionsOptions.'
                    </div>';
        }
    }

    /**
    * Render a search form for the object.
    */
    public function searchForm() {
        $search = $this->object->infoSearch();
        $searchQuery = $this->object->infoSearchQuery();
        $searchValue = urldecode($this->id);
        if ($search!='' || $searchQuery!='') {
            $fieldsSearch = FormField_Text::create(array('name'=>'search', 'value'=>$searchValue));
            $searchInfo = '';
            if ($this->id!='') {
                $searchInfo = '<div class="button buttonBack">
                                    <a href="'.url($this->type.'/listAdmin', true).'">'.__('viewAllItems').'</a>
                                </div>
                                <h2>'.__('resultsFor').': "'.$searchValue.'"'.'</h2>';
            }
            return '<div class="formAdminSearchWrapper">
                        '.Form::createForm($fieldsSearch, array('action'=>url($this->type.'/search', true),
                                                                'submit'=>__('search'),
                                                                'class'=>'formAdminSearch')).'
                        '.$searchInfo.'
                    </div>';
        }
    }

    /**
    * View an element to insert it.
    */
    public function insertView() {
        $uiObjectName = $this->type.'_Ui';
        $uiObject = new $uiObjectName($this->object);
        return $uiObject->renderForm(array('values'=>$this->values,
                                        'action'=>url($this->type.'/insert', true),
                                        'class'=>'formAdmin formAdminInsert'));
    }

    /**
    * Insert an element and return the proper information to render.
    */
    public function insert(){
        $formClass = $this->type.'_Form';
        $form = new $formClass($this->values);
        $errors = $form->isValid();
        $object = $form->get('object');
        if (empty($errors)) {
            try {
                $object->insert($this->values);
            } catch (Exception $e) {
                $form = new $formClass($this->values, array());
                $html = '<div class="message messageError">
                            '.$e->getMessage().'
                        </div>
                        '.$form->createForm($form->createFormFields(), array('action'=>url($this->type.'/insert', true), 'submit'=>__('save'), 'class'=>'formAdmin formAdminInsert'));
                return array('success'=>'0', 'html'=>$html);
            }
            $multipleChoice = (count((array)$this->object->info->info->form->multipleActions) > 0) ? true : false;
            $html = $object->showUi('Admin', array('userType'=>$this->login->get('type'), 'multipleChoice'=>$multipleChoice));
            return array('success'=>'1', 'html'=>$html, 'id'=>$object->id());
        } else {
            $form = new $formClass($this->values, $errors);
            $html = $form->createForm($form->createFormFields(), array('action'=>url($this->type.'/insert', true), 'submit'=>__('save'), 'class'=>'formAdmin formAdminInsert'));
            return array('success'=>'0', 'html'=>$html);
        }
    }

    /**
    * View an element to modify an item.
    */
    public function modifyView() {
        $this->object = $this->object->readObject($this->id);
        $uiObjectName = $this->type.'_Ui';
        $uiObject = new $uiObjectName($this->object);
        $values = array_merge($this->object->valuesArray(), $this->values);
        return $uiObject->renderForm(array_merge(
                                    array('values'=>$values,
                                            'action'=>url($this->type.'/modify', true),
                                            'class'=>'formAdmin formAdminModify'),
                                    array('submit'=>array('save'=>__('save'),
                                            'saveCheck'=>__('saveCheck')))));
    }
    
    /**
    * Modify an element and return the proper information to render.
    */
    public function modify($nested=false) {
        $action = ($nested) ? 'modifyNested' : 'modify';
        if (isset($this->values[$this->object->primary])) {
            $primary = $this->object->primary;
            $idItem = (isset($this->values[$primary.'_oldId'])) ? $this->values[$primary.'_oldId'] : $this->values[$primary];
            $this->object = $this->object->readObject($idItem);
        }
        $formClass = $this->type.'_Form';
        $form = new $formClass($this->object->values, array(), $this->object);
        $form->addValues($this->values);
        $object = $this->object;
        $errors = $form->isValid();
        if (empty($errors)) {
            try {
                $object->modify($this->values);
            } catch (Exception $e) {
                $html = '<div class="message messageError">
                            '.str_replace('<pre>', '', $e->getMessage()).'
                        </div>
                        '.Form::createForm($form->createFormFields(false, $nested), array('action'=>url($this->type.'/'.$action.'/'.$this->id, true), 'submit'=>__('save'), 'class'=>'formAdmin formAdminModify', 'nested'=>$nested));
                return array('success'=>'0', 'html'=>$html);
            }
            $multipleChoice = (count((array)$this->object->info->info->form->multipleActions) > 0) ? true : false;
            $html = $object->showUi('Admin', array('userType'=>$this->login->get('type'), 'multipleChoice'=>$multipleChoice, 'nested'=>$nested));
            return array('success'=>'1', 'id'=>$object->id(), 'html'=>$html);
        } else {
            $form = new $formClass($this->values, $errors);
            $html = Form::createForm($form->createFormFields(false, $nested), array('action'=>url($this->type.'/'.$action.'/'.$this->id, true), 'submit'=>__('save'), 'class'=>'formAdmin formAdminModify', 'nested'=>$nested));
            return array('success'=>'0', 'html'=>$html);
        }
    }

    /**
    * Render the inside menu for certain actions.
    */
    public function menuInside($action='') {
        $items = '';
        $action = ($action!='') ? $action : $this->action;
        switch ($action) {
            case 'listAdmin':
            case 'search':
                if (Permission::canInsert($this->type)) {
                    $items = '<div class="menuSimpleItem menuSimpleItemInsert">
                                <a href="'.url($this->type.'/insertView', true).'">'.__('insertNew').'</a>
                            </div>';
                }
            break;
            case 'insertView':
            case 'insert':
            case 'modifyView':
            case 'modifyViewCheck':
                $items = '<div class="menuSimpleItem menuSimpleItemList">
                            <a href="'.url($this->type.'/listAdmin', true).'">'.__('viewList').'</a>
                        </div>';
            break;
            case 'insertCheck':
                $items = '<div class="menuSimpleItem menuSimpleItemInsert">
                            <a href="'.url($this->type.'/insertView', true).'">'.__('insertNew').'</a>
                        </div>';
                if (Permission::canInsert($this->type)) {
                    $items .= '<div class="menuSimpleItem menuSimpleItemList">
                                <a href="'.url($this->type.'/listAdmin', true).'">'.__('viewList').'</a>
                            </div>';
                }
            break;
            case 'listBack':
                $items = '<div class="menuSimpleItem menuSimpleItemList">
                            <a href="'.url($this->type.'/listAdmin', true).'">'.__('viewList').'</a>
                        </div>';
            break;

        }
        return ($items!='') ? '<nav class="menuSimple">'.$items.'</nav>' : '';
    }

    /**
    * Functions to manage the permissions.
    */
    public function checkLoginAdmin() {
        $this->login = User::loginAdmin();
        $type = UserType::read($this->login->get('idUserType'));
        if ($type->get('managesPermissions')!='1') {
            $permissionsCheck = array('listAdmin'=>'permissionListAdmin',
                                        'insertView'=>'permissionInsert',
                                        'insert'=>'permissionInsert',
                                        'insertCheck'=>'permissionInsert',
                                        'modifyView'=>'permissionModify',
                                        'modifyViewNested'=>'permissionModify',
                                        'modify'=>'permissionModify',
                                        'multiple-activate'=>'permissionModify',
                                        'sortSave'=>'permissionModify',
                                        'delete'=>'permissionDelete',
                                        'multiple-delete'=>'permissionDelete');
            $permissionCheck = $permissionsCheck[$this->action];
            $permission = Permission::readFirst(array('where'=>'objectName="'.$this->type.'" AND idUserType="'.$type->id().'" AND '.$permissionCheck.'="1"'));
            if ($permission->id()=='') {
                if ($this->mode == 'ajax') {
                    return __('permissionsDeny');
                } else {            
                    header('Location: '.url('NavigationAdmin/permissions', true));
                    exit();
                }
            }
        }
    }

}
?>