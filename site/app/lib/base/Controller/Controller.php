<?php
/**
 * @class Controller
 *
 * This is the "controller" component of the MVC pattern used by Asterion.
 * All of the controllers for the content objects extend from this class.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
abstract class Controller
{

    /**
     * The general constructor for the controllers.
     * $GET : Array with the loaded $_GET values.
     * $POST : Array with the loaded $_POST values.
     * $FILES : Array with the loaded $_FILES values.
     */
    public function __construct($GET, $POST, $FILES)
    {
        $this->type = isset($GET['type']) ? $GET['type'] : '';
        $this->objectType = snakeToCamel($this->type);
        $this->action = isset($GET['action']) ? $GET['action'] : 'list';
        $this->id = isset($GET['id']) ? $GET['id'] : '';
        $this->extraId = isset($GET['extraId']) ? $GET['extraId'] : '';
        $this->addId = isset($GET['addId']) ? $GET['addId'] : '';
        $this->params = isset($GET) ? $GET : [];
        $this->values = isset($POST) ? $POST : [];
        $this->files = isset($FILES) ? $FILES : [];
        $this->login = UserAdmin_Login::getInstance();
    }

    /**
     * Function to get the title for a page.
     * By default it uses the title defined in the Parameters.
     */
    public function getTitle()
    {
        $titlePage = Params::param('title_page');
        return (isset($this->title_page)) ? $this->title_page . (($titlePage != '') ? ' - ' . $titlePage : '') : $titlePage;
    }

    /**
     * Function to get the extra header tags for a page.
     * It can be used to load extra CSS or JS files.
     */
    public function getHead()
    {
        return (isset($this->head)) ? $this->head : '';
    }

    /**
     * Function to get the meta_description for a page.
     * By default it uses the meta_description defined in the Parameters.
     */
    public function getMetaDescription()
    {
        return (isset($this->meta_description) && $this->meta_description != '') ? $this->meta_description : Params::param('meta_description');
    }

    /**
     * Function to get the meta-keywords for a page.
     * By default it uses the keywords defined in the Parameters.
     */
    public function getMetaKeywords()
    {
        return (isset($this->meta_keywords)) ? $this->meta_keywords : Params::param('meta_keywords');
    }

    /**
     * Function to get the meta-image for a page.
     * By default it uses the ASTERION_LOGO defined in the configuration file.
     */
    public function getMetaImage()
    {
        $image = (isset($this->metaImage) && $this->metaImage != '') ? $this->metaImage : ASTERION_LOGO;
        $imageFile = str_replace(ASTERION_BASE_URL, ASTERION_BASE_FILE, $image);
        if (is_file($imageFile)) {
            $imageSize = getimagesize($imageFile);
            return '<meta property="og:image" content="' . $image . '" />
                    <meta property="og:image:width" content="' . $imageSize[0] . '" />
                    <meta property="og:image:height" content="' . $imageSize[1] . '" />';
        }
    }

    /**
     * Function to get the url address for a page.
     * A common use is the canonical URL of the current page.
     */
    public function getMetaUrl()
    {
        return (isset($this->meta_url)) ? $this->meta_url : Url::urlActual();
    }

    /**
     * Function to get the mode to render a page.
     * By default it uses the public method.
     * The render goes on the main index.php file.
     */
    public function getMode()
    {
        return (isset($this->mode)) ? $this->mode : 'public';
    }

    /**
     * Main function of the controller.
     * It works as a huge switch that uses the $action attribute defined in the URL.
     * By default this actions are built for the BackEnd since we usually do not modify
     * the objects in the FrontEnd. However for those situations we must override this
     * function in the child controller.
     */
    public function getContent()
    {
        // Check if the tables are created.
        $this->mode = 'admin';
        $this->object = new $this->objectType;
        $this->title_page = __((string) $this->object->info->info->form->title);
        $this->layout = (string) $this->object->info->info->form->layout;
        $this->menuInside = $this->menuInside();
        $ui = new NavigationAdmin_Ui($this);
        switch ($this->action) {
            default:
                header('Location: ' . url($this->type . '/list_admin', true));
                exit();
                break;
            case 'list_admin':
                /**
                 * This is the main action for the BackEnd. If we are in ASTERION_DEBUG mode
                 * it will create the table automatically.
                 */
                $this->checkLoginAdmin();
                $this->content = $this->listAdmin();
                return $ui->render();
                break;
            case 'insert_view':
                /**
                 * This is the action that shows the form to insert a record in the BackEnd.
                 */
                $this->checkLoginAdmin();
                $this->content = $this->insert_view();
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
                if ($insert['success'] == '1') {
                    header('Location: ' . url($this->type . '/insert_check/' . $insert['id'], true));
                    exit();
                } else {
                    $this->message_error = __('errors_form');
                    $this->content = $insert['html'];
                    return $ui->render();
                }
                break;
            case 'modify_view':
            case 'modify_view_check':
            case 'insert_check':
                /**
                 * This is the action that shows the form to check a record insertion.
                 */
                $this->checkLoginAdmin();
                $this->message = ($this->action == 'insert_check' || $this->action == 'modify_view_check') ? __('saved_form') : '';
                $this->content = $this->modify_view();
                return $ui->render();
                break;
            case 'modify':
                /**
                 * This is the action that updates a record when updating it.
                 */
                $this->checkLoginAdmin();
                $modify = $this->modify();
                if ($modify['success'] == '1') {
                    header('Location: ' . url($this->type . '/modify_view_check/' . $modify['id'], true));
                    exit();
                } else {
                    $this->message_error = __('errors_form');
                    $this->content = $modify['html'];
                    return $ui->render();
                }
                break;
            case 'delete':
            case 'delete_ajax':
                /**
                 * This is the action that deletes a record.
                 */
                $this->checkLoginAdmin();
                if ($this->id != '') {
                    $type = new $this->objectType();
                    $object = $type->read($this->id);
                    $object->delete();
                }
                if ($this->action == 'delete_ajax') {
                    $this->mode = 'json';
                    return '{"label": "success"}';
                } else {
                    header('Location: ' . url($this->type . '/list_admin', true));
                }
                exit();
                break;
            case 'delete_image':
                /**
                 * This is the action that deletes a record.
                 */
                $this->checkLoginAdmin();
                $this->mode = 'json';
                if ($this->id != '') {
                    $type = new $this->objectType();
                    $object = $type->read($this->id);
                    $directory = ASTERION_STOCK_FILE . $object->className . '/' . $this->extraId;
                    if (is_dir($directory)) {
                        File::deleteDirectory($directory);
                    }
                    return '{"label": "success"}';
                }
                return '{"label": "error"}';
                break;
            case 'sort_save':
                /**
                 * This is the action that saves the order of a list of records.
                 * It is used when sorting using the BackEnd.
                 */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                $object = new $this->objectType();
                $new_order = (isset($this->values['new_order'])) ? $this->values['new_order'] : [];
                $object->updateOrder($new_order);
                break;
            case 'sort_list':
                /**
                 * This is the action that changes the order of the list.
                 */
                $this->checkLoginAdmin();
                $object = new $this->objectType();
                $info = explode('_', $this->id);
                if (isset($info[1]) && $object->attributeInfo($info[1]) != '') {
                    $orderType = ($info[0] == 'asc') ? 'asc' : 'des';
                    Session::set('ord_' . $this->type, $orderType . '_' . $info[1]);
                }
                header('Location: ' . url($this->type, true));
                exit();
                break;
            case 'multiple_delete':
                /**
                 * This is the action that deletes multiple records at once.
                 */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if (isset($this->values['list_ids'])) {
                    $type = new $this->objectType();
                    foreach ($this->values['list_ids'] as $id) {
                        $object = $type->read($id);
                        $object->delete();
                    }
                }
                break;
            case 'multiple_activate':
            case 'multiple_deactivate':
                /**
                 * This is the action that activates or deactivates multiple records at once.
                 * It just works on records that have an attribute named "active",
                 */
                $this->checkLoginAdmin();
                $this->mode = 'ajax';
                if (isset($this->values['list_ids'])) {
                    $primary = (string) $this->object->info->info->sql->primary;
                    $where = '';
                    foreach ($this->values['list_ids'] as $id) {
                        $where .= $primary . '="' . $id . '" OR ';
                    }
                    $where = substr($where, 0, -4);
                    $active = ($this->action == 'multiple_activate') ? '1' : '0';
                    $query = 'UPDATE ' . Db::prefixTable($this->type) . ' SET active="' . $active . '" WHERE ' . $where;
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
                if ($autocomplete != '') {
                    $where = '';
                    $concat = '';
                    $items = explode('_', $this->id);
                    foreach ($items as $itemIns) {
                        $item = $this->object->attributeInfo($itemIns);
                        $name = (string) $item->name;
                        if (is_object($item) && $name != '') {
                            $concat .= $name . '," ",';
                            $where .= $name . ' LIKE "%' . $autocomplete . '%" OR ';
                        }
                    }
                    $where = substr($where, 0, -4);
                    $concat = 'CONCAT(' . substr($concat, 0, -5) . ')';
                    if ($where != '') {
                        $query = 'SELECT ' . (string) $this->object->info->info->sql->primary . ' as idItem,
                                ' . $concat . ' as infoItem
                                FROM ' . Db::prefixTable($this->type) . '
                                WHERE ' . $where . '
                                ORDER BY ' . $name . ' LIMIT 20';
                        $results = [];
                        $resultsAll = Db::returnAll($query);
                        foreach ($resultsAll as $result) {
                            $resultsIns = [];
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
                    if (isset($this->values['search']) && $this->values['search'] != '') {
                        $searchString = urlencode(html_entity_decode($this->values['search']));
                        header('Location: ' . url($this->type . '/search/' . $searchString, true));
                    } else {
                        header('Location: ' . url($this->type . '/list_admin', true));
                    }
                }
                break;
            case 'export_json':
                /**
                 * This is the action that exports the complete list of objects in JSON format.
                 */
                $this->mode = 'ajax';
                $query = 'SELECT * FROM ' . Db::prefixTable($this->type);
                $items = Db::returnAll($query);
                $file = $this->type . '.json';
                $options = ['content' => json_encode($items), 'contentType' => 'application/json'];
                File::download($file, $options);
                return '';
                break;
        }
    }

    /**
     * Render the list of the items in the administration area.
     */
    public function listAdmin()
    {
        if ((string) $this->object->info->info->form->group != '') {
            return $this->listGroupAdmin();
        }
        $search = $this->object->infoSearch();
        $searchQuery = $this->object->infoSearchQuery();
        $searchQueryCount = $this->object->infoSearchQueryCount();
        $searchValue = urldecode($this->id);
        $sortable_listClass = ($this->object->hasOrd()) ? 'sortable_list' : '';
        $ordObject = explode('_', Session::get('ord_' . $this->type));
        $ordObjectType = (isset($ordObject[0]) && $ordObject[0] == 'asc') ? 'ASC' : 'DESC';
        $options['order'] = $this->orderField();
        if (isset($ordObject[1])) {
            $orderInfo = $this->object->attributeInfo($ordObject[1]);
            $orderInfoItem = (is_object($orderInfo) && (string) $orderInfo->language == "true") ? $ordObject[1] . '_' . Language::active() : $ordObject[1];
            $options['order'] = $orderInfoItem . ' ' . $ordObjectType;
        }
        $options['results'] = (int) $this->object->info->info->form->pager;
        $options['where'] = ($search != '' && $searchValue != '') ? str_replace('#SEARCH', $searchValue, $search) : '';
        $options['query'] = ($searchQuery != '' && $searchValue != '') ? str_replace('#SEARCH', $searchValue, $searchQuery) : '';
        $options['queryCount'] = ($searchQueryCount != '' && $searchValue != '') ? str_replace('#SEARCH', $searchValue, $searchQueryCount) : '';
        $list = new ListObjects($this->objectType, $options);
        $multipleChoice = (count((array) $this->object->info->info->form->multiple_actions->action) > 0);
        $controls_top = $this->multiple_actionsControl() . $this->orderControl();
        $controls_top = ($controls_top != '') ? '<div class="controls_top">' . $controls_top . '</div>' : '';
        return $this->searchForm() . '
                ' . $controls_top . '
                <div class="list_admin list_admin' . $this->type . ' ' . $sortable_listClass . '" data-url="' . url($this->type . '/sort_save/', true) . '">
                    ' . $list->showListPager(['function' => 'Admin', 'message' => '<div class="message">' . __('noItems') . '</div>'], ['user_admin_type' => $this->login->get('type'), 'multipleChoice' => $multipleChoice]) . '
                </div>';
    }

    /**
     * Render the list of the items when is the case of a group.
     */
    public function listGroupAdmin()
    {
        $group = (string) $this->object->info->info->form->group;
        $items = $this->object->getValues($group, true);
        $listItems = '';
        foreach ($items as $key => $item) {
            $sortable_listClass = ($this->object->hasOrd()) ? 'sortable_list' : '';
            $list = new ListObjects($this->objectType, ['where' => $group . '="' . $key . '"',
                'function' => 'Admin',
                'order' => $this->orderField()]);
            $listItems .= '<div class="line_admin_block">
                                <div class="line_admin_title">' . $item . '</div>
                                <div class="line_adminItems">
                                    <div class="list_admin ' . $sortable_listClass . '" data-url="' . url($this->type . '/sort_save/', true) . '">
                                        ' . $list->showList(['function' => 'Admin',
                'message' => '<div class="message">' . __('noItems') . '</div>'],
                ['user_admin_type' => $this->login->get('type')]) . '
                                    </div>
                                </div>
                            </div>';
        }
        return '<div class="line_admin_blockWrapper">' . $listItems . '</div>';
    }

    /**
     * Check for the order field.
     */
    public function orderField()
    {
        $orderAttribute = (string) $this->object->info->info->form->orderBy;
        if ($orderAttribute != '') {
            $orderAttribute = explode(',', $orderAttribute);
            $orderAttribute = explode(' ', $orderAttribute[0]);
            $orderType = (isset($orderAttribute[1]) && $orderAttribute[1] == 'DESC') ? 'DESC' : 'ASC';
            $orderAttribute = $orderAttribute[0];
            $orderInfo = $this->object->attributeInfo($orderAttribute);
            return (is_object($orderInfo) && (string) $orderInfo->language == "true") ? $orderAttribute . '_' . Language::active() . ' ' . $orderType : $orderAttribute . ' ' . $orderType;
        }
    }

    /**
     * Render the order control.
     */
    public function orderControl()
    {
        $orderField = (string) $this->object->info->info->form->orderBy;
        $orderItems = explode(',', $orderField);
        if (count($orderItems) > 1 && $orderField != '') {
            $options = [];
            $selectedItem = Session::get('ord_' . $this->type);
            foreach ($orderItems as $orderItem) {
                $infoOrderItem = explode(' ', trim($orderItem));
                $options['asc_' . $infoOrderItem[0]] = __($infoOrderItem[0]);
                $options['des_' . $infoOrderItem[0]] = __($infoOrderItem[0]) . ' (' . __('reverse') . ')';
            }
            return '<div class="order_actions" data-url="' . url($this->type . '/sort_list/', true) . '">
                        <div class="order_actionsIns">
                            ' . FormField::create('select', ['label' => __('orderBy'), 'name' => 'orderList', 'value' => $options, 'selected' => $selectedItem]) . '
                        </div>
                    </div>';
        }
    }

    /**
     * Render the multiple actions control.
     */
    public function multiple_actionsControl()
    {
        $multiple_actions = (array) $this->object->info->info->form->multiple_actions->action;
        if (count($multiple_actions) > 0) {
            $multiple_actionsOptions = '';
            foreach ($multiple_actions as $multiple_action) {
                $multiple_actionsOptions .= '<div class="multiple_action multipleOption" data-url="' . url($this->type . '/multiple-' . $multiple_action, true) . '">
                                                ' . __($multiple_action . 'Selected') . '
                                            </div>';
            }
            return '<div class="multiple_actions">
                        <div class="multiple_action multiple_action_check_all">
                            ' . FormField_Checkbox::create(['name' => 'checkboxList']) . '
                        </div>
                        ' . $multiple_actionsOptions . '
                    </div>';
        }
    }

    /**
     * Render a search form for the object.
     */
    public function searchForm()
    {
        $search = $this->object->infoSearch();
        $searchQuery = $this->object->infoSearchQuery();
        $searchValue = urldecode($this->id);
        if ($search != '' || $searchQuery != '') {
            $fieldsSearch = FormField_Text::create(['name' => 'search', 'value' => $searchValue]);
            $searchInfo = '';
            if ($this->id != '') {
                $searchInfo = '<div class="button button_back">
                                    <a href="' . url($this->type . '/list_admin', true) . '">' . __('viewAllItems') . '</a>
                                </div>
                                <h2>' . __('resultsFor') . ': "' . $searchValue . '"' . '</h2>';
            }
            return '<div class="form_admin_search_wrapper">
                        ' . Form::createForm($fieldsSearch, ['action' => url($this->type . '/search', true),
                'submit' => __('search'),
                'class' => 'form_admin_search']) . '
                        ' . $searchInfo . '
                    </div>';
        }
    }

    /**
     * View an element to insert it.
     */
    public function insert_view()
    {
        $uiObjectName = $this->objectType . '_Ui';
        $uiObject = new $uiObjectName($this->object);
        return $uiObject->renderForm(['values' => $this->values,
            'action' => url($this->type . '/insert', true),
            'class' => 'form_admin form_admin_insert']);
    }

    /**
     * Insert an element and return the proper information to render.
     */
    public function insert()
    {
        $formClass = $this->objectType . '_Form';
        $form = new $formClass($this->values);
        $errors = $form->isValid();
        $object = $form->get('object');
        if (empty($errors)) {
            try {
                $object->insert($this->values);
            } catch (Exception $e) {
                $form = new $formClass($this->values, []);
                $html = '<div class="message message_error">
                            ' . $e->getMessage() . '
                        </div>
                        ' . $form->createForm($form->createFormFields(), ['action' => url($this->type . '/insert', true), 'submit' => __('save'), 'class' => 'form_admin form_admin_insert']);
                return ['success' => '0', 'html' => $html];
            }
            $multipleChoice = (count((array) $this->object->info->info->form->multiple_actions) > 0) ? true : false;
            $html = $object->showUi('Admin', ['user_admin_type' => $this->login->get('type'), 'multipleChoice' => $multipleChoice]);
            return ['success' => '1', 'html' => $html, 'id' => $object->id()];
        } else {
            $form = new $formClass($this->values, $errors);
            $html = $form->createForm($form->createFormFields(), ['action' => url($this->type . '/insert', true), 'submit' => __('save'), 'class' => 'form_admin form_admin_insert']);
            return ['success' => '0', 'html' => $html];
        }
    }

    /**
     * View an element to modify an item.
     */
    public function modify_view()
    {
        $this->object = $this->object->read($this->id);
        $uiObjectName = $this->objectType . '_Ui';
        $uiObject = new $uiObjectName($this->object);
        $values = array_merge($this->object->valuesArray(), $this->values);
        return $uiObject->renderForm(array_merge(
            ['values' => $values,
                'action' => url($this->type . '/modify', true),
                'class' => 'form_admin form_admin_modify'],
            ['submit' => ['save' => __('save')]]));
    }

    /**
     * Modify an element and return the proper information to render.
     */
    public function modify()
    {
        if (isset($this->values[$this->object->primary])) {
            $primary = $this->object->primary;
            $idItem = (isset($this->values[$primary . '_oldId'])) ? $this->values[$primary . '_oldId'] : $this->values[$primary];
            $this->object = $this->object->read($idItem);
        }
        $formClass = $this->type . '_Form';
        $form = new $formClass($this->object->values, [], $this->object);
        $form->addValues($this->values);
        $object = $this->object;
        $errors = $form->isValid();
        if (empty($errors)) {
            try {
                $object->modify($this->values);
            } catch (Exception $e) {
                $html = '<div class="message message_error">
                            ' . str_replace('<pre>', '', $e->getMessage()) . '
                        </div>
                        ' . Form::createForm($form->createFormFields(false), ['action' => url($this->type . '/modify/' . $this->id, true), 'submit' => __('save'), 'class' => 'form_admin form_admin_modify']);
                return ['success' => '0', 'html' => $html];
            }
            $multipleChoice = (count((array) $this->object->info->info->form->multiple_actions) > 0) ? true : false;
            $html = $object->showUi('Admin', ['user_admin_type' => $this->login->get('type'), 'multipleChoice' => $multipleChoice]);
            return ['success' => '1', 'id' => $object->id(), 'html' => $html];
        } else {
            $form = new $formClass($this->values, $errors);
            $html = Form::createForm($form->createFormFields(false), ['action' => url($this->type . '/modify/' . $this->id, true), 'submit' => __('save'), 'class' => 'form_admin form_admin_modify']);
            return ['success' => '0', 'html' => $html];
        }
    }

    /**
     * Render the inside menu for certain actions.
     */
    public function menuInside()
    {
        $items = '';
        if (Permission::canInsert($this->type)) {
            $items = Ui::menuAdminInside($this->type . '/insert_view', 'plus', 'insert_new');
        }
        if (in_array($this->action, ['insert_view', 'insert_check', 'modify_view', 'modify_view_check'])) {
            $items .= Ui::menuAdminInside($this->type . '/list_admin', 'list', 'view_list');
        }
        return ($items != '') ? '<nav class="menu_simple">' . $items . '</nav>' : '';
    }

    /**
     * Functions to manage the permissions.
     */
    public function checkLoginAdmin()
    {
        $this->login = UserAdmin::loginAdmin();
        $userAdminType = (new UserAdminType)->read($this->login->get('id_user_admin_type'));
        if ($userAdminType->get('manages_permissions') != '1') {
            $permissionsCheck = [
                'list_admin' => 'permission_list_admin',
                'insert' => 'permission_insert',
                'insert_view' => 'permission_insert',
                'insert_check' => 'permission_insert',
                'modify' => 'permission_modify',
                'modify_view' => 'permission_modify',
                'modify_view_check' => 'permission_modify',
                'multiple_activate' => 'permission_modify',
                'sort_save' => 'permission_modify',
                'delete' => 'permission_delete',
                'multiple_delete' => 'permission_delete',
            ];
            $permissionCheck = $permissionsCheck[$this->action];
            $permission = (new Permission)->readFirst(['where' => 'object_name="' . $this->type . '" AND id_user_admin_type="' . $userAdminType->id() . '" AND ' . $permissionCheck . '="1"']);
            if ($permission->id() == '') {
                if ($this->mode == 'ajax') {
                    return __('permissionsDeny');
                } else {
                    header('Location: ' . url('NavigationAdmin/permissions', true));
                    exit();
                }
            }
        }
    }

}
