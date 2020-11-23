<?php
/**
 * @class NavigationAdmin_Ui
 *
 * This class manages the UI for the NavigationAdmin object.
 * Here we render the template for the administration area.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class NavigationAdmin_Ui extends Ui
{

    /**
     * Render the page using different layouts
     */
    public function render()
    {
        $layout_page = (isset($this->object->layout_page)) ? $this->object->layout_page : '';
        $title = (isset($this->object->title_page) && $this->object->title_page != '') ? '<h1>' . $this->object->title_page . '</h1>' : '';
        $message = (isset($this->object->message) && $this->object->message != '') ? '<div class="message">' . $this->object->message . '</div>' : '';
        $message_alert = (isset($this->object->message_alert) && $this->object->message_alert != '') ? '<div class="message message_alert">' . $this->object->message_alert . '</div>' : '';
        $message_error = (isset($this->object->message_error) && $this->object->message_error != '') ? '<div class="message message_error">' . $this->object->message_error . '</div>' : '';
        $menuInside = (isset($this->object->menuInside)) ? $this->object->menuInside : '';
        $content = (isset($this->object->content)) ? $this->object->content : '';
        switch ($layout_page) {
            default:
                return '<div class="content_wrapper content_wrapper-' . $this->object->type . '">
                            ' . $this->header() . '
                            <div class="content_ins">
                                <div class="content_menu">
                                    ' . $this->renderMenu() . '
                                </div>
                                <div class="content_ins_wrapper">
                                    <div class="content">
                                        <div class="content_top">
                                            <div class="content_top_left">
                                                ' . $title . '
                                            </div>
                                            <div class="content_top_right">
                                                ' . $menuInside . '
                                            </div>
                                        </div>
                                        ' . $message_error . '
                                        ' . $message_alert . '
                                        ' . $message . '
                                        ' . $content . '
                                        ' . $this->footer() . '
                                    </div>
                                </div>
                            </div>
                        </div>';
                break;
            case 'simple':
                return '<div class="content_wrapper content_wrapper-' . $this->object->type . '">
                            ' . $this->header() . '
                            <div class="content_' . $layout_page . '">
                                ' . $title . '
                                ' . $message_error . '
                                ' . $message_alert . '
                                ' . $message . '
                                ' . $content . '
                                ' . $this->footer() . '
                            </div>
                        </div>';
                break;
            case 'clear':
                return '<div class="content_wrapper content_wrapper-' . $this->object->type . '">
                            ' . $this->headerSimple() . '
                            <div class="content_' . $layout_page . '">
                                ' . $title . '
                                ' . $message_error . '
                                ' . $message_alert . '
                                ' . $message . '
                                ' . $content . '
                            </div>
                        </div>';
                break;
        }
    }

    /**
     * Render the header for the page
     */
    public function header()
    {
        return '<header class="header_wrapper">
                    <div class="header_ins">
                        <div class="header_left">
                            <div class="logo">
                                <a href="' . url('', true) . '">' . Params::param('title_page') . '</a>
                            </div>
                        </div>
                        <div class="header_right">
                            ' . UserAdmin_Ui::infoHtml() . '
                            ' . Language_Ui::showLanguages(true) . '
                        </div>
                    </div>
                </header>';
    }

    /**
     * Render the simple header for the page
     */
    public function headerSimple($complete = true)
    {
        return '<header class="header_wrapper">
                    <div class="header_ins">
                        <div class="logo"><span>' . ASTERION_TITLE . '</span></div>
                    </div>
                </header>';
    }

    /**
     * Render the footer for the page
     */
    public function footer()
    {
        return '<footer class="footer">
                    ' . HtmlSectionAdmin::show('footer') . '
                </footer>';
    }

    /**
     * Render the menu for the page based on the user admin type.
     */
    public function renderMenu()
    {
        $this->login = UserAdmin_Login::getInstance();
        $this->user_admin_type = (new UserAdminType)->read($this->login->get('id_user_admin_type'));
        if ($this->user_admin_type->id() != '') {
            $menuItems = '';
            $objectNames = File::scanDirectoryObjectsBase();
            $menuItems .= $this->renderMenuObjects($objectNames, 'menu_side_item_base');
            $objectNames = File::scanDirectoryObjectsApp();
            $menuItems .= $this->renderMenuObjects($objectNames, 'menu_side_item_app');
            if ($this->user_admin_type->get('manages_permissions') == '1') {
                $menuItems .= '<div class="menu_side_item menu_side_item_admin">
                                    <a href="' . url('language', true) . '">
                                        <i class="fa fa-language"></i>
                                        <span>' . __('languages') . '</span>
                                    </a>
                                </div>
                                <div class="menu_side_item menu_side_item_admin">
                                    <a href="' . url('translation', true) . '">
                                        <i class="fa fa-comment-alt"></i>
                                        <span>' . __('translations') . '</span>
                                    </a>
                                </div>
                                <div class="menu_side_item menu_side_item_admin">
                                    <a href="' . url('permission', true) . '">
                                        <i class="fa fa-users"></i>
                                        <span>' . __('permissions') . '</span>
                                    </a>
                                </div>
                                <div class="menu_side_item menu_side_item_admin">
                                    <a href="' . url('navigation_admin/backup', true) . '">
                                        <i class="fa fa-download"></i>
                                        <span>' . __('backup') . '</span>
                                    </a>
                                </div>
                                <div class="menu_side_item menu_side_item_admin">
                                    <a href="' . url('navigation_admin/cache', true) . '">
                                        <i class="fa fa-download"></i>
                                        <span>' . __('cache') . '</span>
                                    </a>
                                </div>
                                <div class="menu_side_item menu_side_item_admin">
                                    <a href="' . url('log', true) . '">
                                        <i class="fa fa-laptop"></i>
                                        <span>' . __('logs') . '</span>
                                    </a>
                                </div>';
            }
            return '<nav class="menu_side">
                        ' . $menuItems . '
                        <div class="menu_side_item menu_side_item_logout">
                            <a href="' . url('user_admin/logout', true) . '">
                                <i class="fa fa-power-off"></i>
                                <span>' . __('logout') . '</span>
                            </a>
                        </div>
                    </nav>';
        }
    }

    /**
     * Render the menu for a list of objects.
     */
    public function renderMenuObjects($objectNames, $class)
    {
        $html = '';
        $menuItems = [];
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $objectHidden = (string) $object->info->info->form->hiddenAdminMenu;
            $objectGroupMenu = (string) $object->info->info->form->groupMenu;
            $objectGroupMenu = ($objectGroupMenu != '') ? $objectGroupMenu : '';
            if ($objectHidden != 'true') {
                if (!isset($menuItems[$objectGroupMenu])) {
                    $menuItems[$objectGroupMenu] = [];
                }
                array_push($menuItems[$objectGroupMenu], ['name' => (string) $object->info->name, 'title' => __((string) $object->info->info->form->title)]);
            }
        }
        ksort($menuItems);
        foreach ($menuItems as $menuItemGroupKey => $menuItemGroup) {
            usort($menuItemGroup, function($a, $b) {return strcmp($a['title'], $b['title']);});
            $htmlGroup = '';
            foreach ($menuItemGroup as $menuItem) {
                if ($this->user_admin_type->get('manages_permissions') == '1') {
                    $htmlGroup .= '<div class="menu_side_item menu_side_item-' . $menuItem['name'] . ' ' . $class . '">
                                        <a href="' . url(camelToSnake($menuItem['name']), true) . '">
                                            <i class="fa fa-arrow-right"></i>
                                            <span>' . __($menuItem['title']) . '</span>
                                        </a>
                                    </div>';
                } else {
                    $permission = (new Permission)->readFirst(['where' => 'object_name="' . $menuItem['name'] . '" AND id_user_admin_type="' . $this->user_admin_type->id() . '"']);
                    if ($permission->get('permission_list_admin') == '1') {
                        $htmlGroup .= '<div class="menu_side_item menu_side_item-' . $menuItem['name'] . ' ' . $class . '">
                                            <a href="' . url(camelToSnake($menuItem['name']), true) . '">
                                                <i class="fa fa-arrow-right"></i>
                                                <span>' . __($menuItem['title']) . '</span>
                                            </a>
                                        </div>';
                    }
                }
            }
            $html .= ($htmlGroup != '' && $menuItemGroupKey != '') ? '<div class="menu-side-wrapper">
                                                                        <div class="menu-side-title">' . __($menuItemGroupKey) . '</div>
                                                                        ' . $htmlGroup . '
                                                                    </div>' : $htmlGroup;
        }
        return $html;
    }

}
