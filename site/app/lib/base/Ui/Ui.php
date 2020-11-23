<?php
/**
 * @class Ui
 *
 * This is the main class for the UserAdmin Interface.
 * It is used mainly to render HTML blocks for the different objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Ui
{

    /**
     * The constructor of the object.
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Render a div with the basic information.
     */
    public function renderPublic()
    {
        return '<div class="item item_' . $this->object->className . '">
                    ' . $this->object->getBasicInfo() . '
                </div>';
    }

    /**
     * Render a link.
     */
    public function renderLink()
    {
        return $this->object->link();
    }

    /**
     * Render the simple information for a CSV format.
     */
    public function renderCsv()
    {
        return $this->object->getBasicInfo() . ',';
    }

    /**
     * Render the object to send it within an email.
     */
    public function renderEmail()
    {
        $content = '';
        foreach ($this->object->info->attributes->attribute as $item) {
            $label = (string) $item->label;
            $name = (string) $item->name;
            $type = (string) $item->type;
            switch (Db_ObjectType::baseType($type)) {
                case 'text':
                    $content .= '<strong>' . __($label) . '</strong>: ' . $this->object->get($name) . '<br/>';
                    break;
                case 'textarea':
                    $content .= '<strong>' . __($label) . '</strong>: ' . nl2br($this->object->get($name)) . '<br/>';
                    break;
                case 'select':
                case 'radio':
                    $content .= '<strong>' . __($label) . '</strong>: ' . $this->object->label($name) . '<br/>';
                    break;
                case 'date':
                case 'select_date':
                    $content .= '<strong>' . __($label) . '</strong>: ' . Date::sqlText($this->object->get($name), true) . '<br/>';
                    break;
                case 'checkbox':
                    $value = ($this->object->get($name) == 1) ? __('yes') : __('no');
                    $content .= '<strong>' . __($label) . '</strong>: ' . $value . '<br/>';
                    break;
            }
        }
        return '<p>' . $content . '</p>';
    }

    /**
     * Render the object for the admin area.
     */
    public function renderAdmin($options = [])
    {
        $class = (isset($options['class'])) ? $options['class'] : '';
        $permissions = Permission::getAll($this->object->className);
        $canModify = ($permissions['permission_modify'] == '1') ? $this->modify() : '';
        $canDelete = ($permissions['permission_delete'] == '1') ? $this->delete(true) : '';
        $dataOrd = ($permissions['permission_modify'] == '1') ? 'data-id="' . $this->object->id() . '"' : '';
        $viewPublic = ((string) $this->object->info->info->form->viewPublic == 'true') ? $this->view() : '';
        $layout = 'line_adminLayout' . (string) $this->object->info->info->form->layout;
        return '<div class="line_admin' . $this->object->className . ' line_admin ' . $class . '" ' . $dataOrd . '>
                    <div class="line_admin_wrapper">
                        ' . $this->renderAdminInside() . '
                        <div class="line_admin_cell line_admin_options">
                            ' . $viewPublic . '
                            ' . $canModify . '
                            ' . $canDelete . '
                        </div>
                    </div>
                </div>';
    }

    /**
     * Render the information of the object for the admin area.
     */
    public function renderAdminInside($options = [])
    {
        $permissions = Permission::getAll($this->object->className);
        $canOrder = ($permissions['permission_modify'] == '1' && $this->object->hasOrd()) ? $this->order() : '';
        $canOrder = ($canOrder != '') ? '<div class="line_admin_cell line_adminOrder">' . $canOrder . '</div>' : '';
        $multipleChoice = '';
        if (isset($options['multipleChoice']) && $options['multipleChoice'] == true) {
            $multipleChoice .= '<div class="line_admin_cell line_admin_checkbox">
                                    ' . FormField_Checkbox::create(['name' => $this->object->id()]) . '
                                </div>';
        }
        $label = ($permissions['permission_modify'] == '1') ? $this->label(true) : $this->label(false);
        return '<div class="line_admin_wrapper_ins">
                    ' . $multipleChoice . '
                    ' . $canOrder . '
                    <div class="line_admin_cell line_admin_label">
                        ' . $label . '
                    </div>
                </div>';
    }

    /**
     * Render the object as a sitemap url.
     */
    public function renderSitemap($options = [])
    {
        $changefreq = isset($options['changefreq']) ? $options['changefreq'] : 'weekly';
        $priority = isset($options['priority']) ? $options['priority'] : '1';
        $xml = '<url>
                    <loc>' . $this->object->url() . '</loc>
                    <lastmod>' . date('Y-m-d') . '</lastmod>
                    <changefreq>' . $changefreq . '</changefreq>
                    <priority>' . $priority . '</priority>
                </url>';
        return Text::minimize($xml);
    }

    /**
     * Render the object as a sitemap url.
     */
    public function renderRss($options = [])
    {
        $xml = ' <item>
                    <title>' . $this->object->getBasicInfo() . '</title>
                    <link>' . $this->object->url() . '</link>
                    <description><![CDATA[' . $this->object->get('description') . ']]></description>
                </item>';
        return Text::minimize($xml);
    }

    /**
     * Render a form for the object.
     */
    public function renderForm($options = [])
    {
        $values = (isset($options['values'])) ? $options['values'] : '';
        $action = (isset($options['action'])) ? $options['action'] : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $submit = (isset($options['submit'])) ? $options['submit'] : __('save');
        $formClass = $this->object->className . '_Form';
        $objectForm = new $formClass;
        $form = $objectForm->fromArray($values);
        return Form::createForm($form->createFormFields(false),
            ['action' => $action, 'submit' => $submit, 'class' => $class]);
    }

    /**
     * Create a label in the admin using the information in the XML file.
     */
    public function label($canModify = false)
    {
        if (isset($this->object->info->info->form->templateItemAdmin)) {
            $html = (string) $this->object->info->info->form->templateItemAdmin->asXML();
            $html = str_replace('<templateItemAdmin>', '', $html);
            $html = str_replace('</templateItemAdmin>', '', $html);
            $attributes = Text::arrayWordsStarting('##', $html);
            foreach ($attributes as $attribute) {
                $attribute = str_replace('##', '', $attribute);
                $info = $this->object->attributeInfo($attribute);
                $infoType = (isset($info->type)) ? $info->type : '';
                $labelAttribute = $this->object->get($attribute);
                $html = str_replace('##' . $attribute, $labelAttribute, $html);
            }
            $attributes = Text::arrayWordsStarting('#', $html);
            foreach ($attributes as $attribute) {
                $attribute = str_replace('#', '', $attribute);
                $info = $this->object->attributeInfo($attribute);
                $infoType = (isset($info->type)) ? $info->type : '';
                switch ($infoType) {
                    default:
                        $labelAttribute = $this->object->get($attribute);
                        break;
                    case 'linkid_autoincrement':
                        $refObjectName = (string) $info->refObject;
                        if ($refObjectName != '') {
                            $refObject = new $refObjectName;
                            $refObject = $refObject->read($this->object->get($attribute));
                            $labelAttribute = $refObject->getBasicInfoAdmin();
                        }
                        break;
                    case 'textarea_code':
                        $labelAttribute = htmlentities($this->object->get($attribute));
                        break;
                    case 'hidden_login':
                    case 'hidden_user_admin':
                        $userAdmin = (new UserAdmin)->read($this->object->get($attribute));
                        $labelAttribute = ($userAdmin->id() != '') ? $userAdmin->getBasicInfo() : '';
                        break;
                    case 'select':
                    case 'select_varchar':
                        $labelAttribute = $this->object->label($attribute);
                        break;
                    case 'checkbox':
                        $labelAttribute = ($this->object->get($attribute) == '1') ? __('yes') : __('no');
                        break;
                    case 'date_text':
                        $labelAttribute = Date::sqlText($this->object->get($attribute));
                        break;
                    case 'file':
                        if ((string) $info->mode == 'image') {
                            $labelAttribute = $this->object->getImageIcon($attribute);
                        } else {
                            $labelAttribute = $this->object->getFileLink($attribute);
                        }
                        break;
                }
                $html = str_replace('#' . $attribute, $labelAttribute, $html);
            }
            $wordsTranslate = Text::arrayWordsStarting('_', $html);
            foreach ($wordsTranslate as $wordTranslate) {
                $html = str_replace($wordTranslate, __(str_replace('_', '', $wordTranslate)), $html);
            }
        } else {
            $html = $this->object->getBasicInfoAdmin();
        }
        $html = ($canModify == '1') ? '<a href="' . $this->linkModify() . '">' . $html . '</a>' : $html;
        return '<div class="label">' . $html . '</div>';
    }

    /**
     * Render the label text when multiple is active.
     */
    public function labelMultiple($objectName, $objectNameConnector, $separator = ', ')
    {
        $objectNameIns = new $objectName();
        $query = 'SELECT DISTINCT o.*
                    FROM ' . Db::prefixTable($objectName) . ' o
                    JOIN ' . Db::prefixTable($objectNameConnector) . ' bo
                    ON (bo.' . $this->object->primary . '="' . $this->object->id() . '" AND bo.' . $objectNameIns->primary . '=o.' . $objectNameIns->primary . ')';
        $objects = $objectNameIns->readListQuery($query);
        $html = '';
        foreach ($objects as $object) {
            $html .= $object->getBasicInfo() . $separator;
        }
        $html = substr($html, 0, -1 * strlen($separator));
        return $html;
    }

    /**
     * Return the link for modification, in an admin context.
     */
    public function linkModify()
    {
        return url(camelToSnake($this->object->className) . '/modify_view/' . $this->object->id(), true);
    }

    /**
     * Return the link for deletion, in an admin context.
     */
    public function linkDelete($ajax = false)
    {
        return url(camelToSnake($this->object->className) . '/' . (($ajax) ? 'delete_ajax' : 'delete') . '/' . $this->object->id(), true);
    }

    /**
     * Return a div with the delete link.
     */
    public function delete($ajax = false)
    {
        return '<div class="icon_side icon_delete ' . (($ajax) ? 'icon_delete_ajax' : '') . '">
                    <a href="' . $this->linkDelete($ajax) . '">
                        <i class="fa fa-trash"></i>
                        <span>' . __('delete') . '</span>
                    </a>
                </div>';
    }

    /**
     * Return a div with the modify link.
     */
    public function modify()
    {
        return '<div class="icon_side icon_modify">
                    <a href="' . $this->linkModify() . '">
                        <i class="fa fa-edit"></i>
                        <span>' . __('modify') . '</span>
                    </a>
                </div>';
    }

    /**
     * Return a div with the view public link.
     */
    public function view()
    {
        return '<div class="icon_side icon_view">
                    <a href="' . $this->object->url() . '" target="_blank">
                        <i class="fa fa-eye"></i>
                        <span>' . __('view') . '</span>
                    </a>
                </div>';
    }

    /**
     * Return a div with the move handle.
     */
    public function order()
    {
        return '<div class="icon_side icon_handle">
                    <i class="fa fa-arrows-alt"></i>
                </div>';
    }

    static public function menuAdminInside($url, $icon, $label) {
        return '<div class="menu_simple_item menu_simple_item_'.$label.'">
                    <a href="' . url($url, true) . '">
                        <i class="fa fa-'.$icon.'"></i>
                        <span>' . __($label) . '</span>
                    </a>
                </div>';
    }

    /**
     * Return a div with the share and print elements.
     */
    public function share($options = [])
    {
        $title = (isset($options['title'])) ? '<div class="share_options_title">' . $options['title'] . '</div>' : '';
        $content = '';
        foreach ($options['share'] as $share) {
            switch ($share) {
                default:
                    $content .= $share;
                    break;
                case 'facebook':
                    $link = 'http://www.facebook.com/sharer/sharer.php?u=' . urlencode($this->object->url());
                    $content .= '<a href="' . $link . '" target="_blank" class="share_option share_option_facebook">
                                <i class="fa fa-facebook-f"></i>
                                <span>Facebook</span>
                            </a>';
                    break;
                case 'twitter':
                    $link = 'http://www.twitter.com/share?text=' . urlencode($this->object->getBasicInfo()) . '&url=' . urlencode($this->object->url());
                    $content .= '<a href="' . $link . '" target="_blank" class="share_option share_option_twitter">
                                <i class="fa fa-twitter"></i>
                                <span>Twitter</span>
                            </a>';
                    break;
                case 'linkedin':
                    $link = 'https://www.linkedin.com/cws/share?url=' . urlencode($this->object->url());
                    $content .= '<a href="' . $link . '" target="_blank" class="share_option share_option_linkedin">
                                <i class="fa fa-linked-in"></i>
                                <span>LinkedIn</span>
                            </a>';
                    break;
                case 'print':
                    $link = 'javascript:window.print()';
                    $content .= '<a href="' . $link . '" class="share_option share_option_print">
                                <i class="fa fa-print"></i>
                                <span>' . __('print') . '</span>
                            </a>';
                    break;
            }
        }
        return '<div class="share_options">
                    ' . $title . '
                    <div class="share_options_buttons">
                        ' . $content . '
                    </div>
                </div>';
    }

}
