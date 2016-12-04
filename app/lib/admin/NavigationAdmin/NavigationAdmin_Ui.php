<?php
/**
* @class NavigationAdmin_Ui
*
* This class manages the UI for the NavigationAdmin object.
* Here we render the template for the administration area.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class NavigationAdmin_Ui extends Ui{

    /**
    * Render the page using different layouts
    */
    public function render() {
        $layoutPage = (isset($this->object->layoutPage)) ? $this->object->layoutPage : '';
        $title = (isset($this->object->titlePage) && $this->object->titlePage!='') ? '<h1>'.$this->object->titlePage.'</h1>' : '';
        $message = (isset($this->object->message) && $this->object->message!='') ? '<div class="message">'.$this->object->message.'</div>' : '';
        $messageAlert = (isset($this->object->messageAlert) && $this->object->messageAlert!='') ? '<div class="message messageAlert">'.$this->object->messageAlert.'</div>' : '';
        $messageError = (isset($this->object->messageError) && $this->object->messageError!='') ? '<div class="message messageError">'.$this->object->messageError.'</div>' : '';
        $menuInside = (isset($this->object->menuInside)) ? $this->object->menuInside : '';
        $content = (isset($this->object->content)) ? $this->object->content : '';
        switch ($layoutPage) {
            default:
                return '<div class="contentWrapper contentWrapper-'.$this->object->type.'">
                            '.$this->header().'
                            <div class="contentIns">
                                <div class="contentMenu">
                                    '.$this->renderMenu().'
                                </div>
                                <div class="contentInsWrapper">
                                    <div class="content">
                                        <div class="contentTop">
                                            <div class="contentTopLeft">
                                                '.$title.'
                                            </div>
                                            <div class="contentTopRight">
                                                '.$menuInside.'
                                            </div>
                                        </div>
                                        '.$messageError.'
                                        '.$messageAlert.'
                                        '.$message.'
                                        '.$content.'
                                        '.$this->footer().'
                                    </div>
                                </div>
                            </div>
                        </div>';
            break;
            case 'simple':
                return '<div class="contentWrapper contentWrapper-'.$this->object->type.'">
                            '.$this->header().'
                            <div class="contentSimple">
                                '.$title.'
                                '.$messageError.'
                                '.$messageAlert.'
                                '.$message.'
                                '.$content.'
                                '.$this->footer().'
                            </div>
                        </div>';
            break;
        }
    }

    /**
    * Render the header for the page
    */
    public function header() {
        return '<header class="headerWrapper">
                    <div class="headerIns">
                        <div class="headerLeft">
                            <div class="logo">
                                <a href="'.url('', true).'">'.Params::param('metainfo-titlePage').'</a>
                            </div>
                        </div>
                        <div class="headerRight">
                            '.User_Ui::infoHtml().'
                            '.Lang_Ui::showLangs(true).'
                        </div>
                    </div>
                </header>';
    }

    /**
    * Render the footer for the page
    */
    public function footer() {
        return '<footer class="footer">
                    '.HtmlSectionAdmin::show('footer').'
                </footer>';
    }

    /**
    * Render the menu for the page based on the user type.
    */
    public function renderMenu() {
        $this->login = User_Login::getInstance();
        $this->userType = UserType::read($this->login->get('idUserType'));
        if ($this->userType->id()!='') {
            $menuItems = '';
            $objectNames = File::scanDirectoryObjectsBase();
            $menuItems .= $this->renderMenuObjects($objectNames, 'menuSideItemBase');
            $objectNames = File::scanDirectoryObjectsApp();
            $menuItems .= $this->renderMenuObjects($objectNames, 'menuSideItemApp');
            if ($this->userType->get('managesPermissions')=='1') {
                $menuItems .= '
                                <div class="menuSideItem menuSideItem-langs">
                                    <a href="'.url('Lang', true).'">'.__('langs').'</a>
                                </div>
                                <div class="menuSideItem menuSideItem-permissions">
                                    <a href="'.url('Permission', true).'">'.__('permissions').'</a>
                                </div>
                                <div class="menuSideItem menuSideItem-backup">
                                    <a href="'.url('NavigationAdmin/backup', true).'">'.__('backup').'</a>
                                </div>
                                <div class="menuSideItem menuSideItem-cache">
                                    <a href="'.url('NavigationAdmin/cache', true).'">'.__('cache').'</a>
                                </div>';
            }
            $menuList = new ListObjects('UserTypeMenu', array('where'=>'idUserType="'.$this->userType->id().'"', 'order'=>'ord'));
            return '<nav class="menuSide">
                        '.$menuList->showList(array('function'=>'Menu')).'
                        '.$menuItems.'
                        <div class="menuSideItem menuSideItem-logout">
                            <a href="'.url('User/logout', true).'">'.__('logout').'</a>
                        </div>
                    </nav>';
        }
    }

    /**
    * Render the menu for a list of objects.
    */
    public function renderMenuObjects($objectNames, $class) {
        $menuItems = '';
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $objectName = (string)$object->info->name;
            $objectTitle = (string)$object->info->info->form->title;
            $objectHidden = (string)$object->info->info->form->hiddenAdminMenu;
            if ($objectHidden != 'true') {
                if ($this->userType->get('managesPermissions')=='1') {
                    $menuItems .= '<div class="menuSideItem menuSideItem-'.$objectName.' '.$class.'">
                                        <a href="'.url($objectName, true).'">'.__($objectTitle).'</a>
                                    </div>';
                } else {
                    $permission = Permission::readFirst(array('where'=>'objectName="'.$objectName.'" AND idUserType="'.$this->userType->id().'"'));
                    if ($permission->get('permissionListAdmin')=='1') {
                        $menuItems .= '<div class="menuSideItem menuSideItem-'.$objectName.' '.$class.'">
                                            <a href="'.url($objectName, true).'">'.__($objectTitle).'</a>
                                        </div>';
                    }
                }
            }
        }
        return $menuItems;
    }

}
?>