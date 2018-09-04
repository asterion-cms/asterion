<?php
/**
* @class Ui
*
* This is the main class for the User Interface.
* It is used mainly to render HTML blocks for the different objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Ui {

    /**
    * The constructor of the object.
    */
    public function __construct ($object) {
        $this->object = $object;
    }

    /**
    * Render a div with the basic information.
    */
    public function renderPublic() {
        return '<div class="item item_'.$this->object->className.'">
                    '.$this->object->getBasicInfo().'
                </div>';
    }

    /**
    * Render a link.
    */
    public function renderLink() {
        return $this->object->link();
    }

    /**
    * Render the simple information for a CSV format.
    */
    public function renderCsv() {
        return $this->object->getBasicInfo().',';
    }

    /**
    * Render the object to send it within an email.
    */
    public function renderEmail() {
        $content = '';
        foreach($this->object->info->attributes->attribute as $item) {
            $label = (string)$item->label;
            $name = (string)$item->name;
            $type = (string)$item->type;
            switch (Db_ObjectType::baseType($type)) {
                case 'text':
                    $content .= '<strong>'.__($label).'</strong>: '.$this->object->get($name).'<br/>';
                break;
                case 'textarea':
                    $content .= '<strong>'.__($label).'</strong>: '.nl2br($this->object->get($name)).'<br/>';
                break;
                case 'select':
                case 'radio':
                    $content .= '<strong>'.__($label).'</strong>: '.$this->object->label($name).'<br/>';
                break;
                case 'date':
                case 'selectDate':
                    $content .= '<strong>'.__($label).'</strong>: '.Date::sqlText($this->object->get($name), true).'<br/>';
                break;
                case 'checkbox':
                    $value = ($this->object->get($name)==1) ? __('yes') : __('no');
                    $content .= '<strong>'.__($label).'</strong>: '.$value.'<br/>';
                break;
            }
        }
        return '<p>'.$content.'</p>';
    }

    /**
    * Render the object for the admin area.
    */
    public function renderAdmin($options=array()) {
        $userType = (isset($options['userType'])) ? $options['userType'] : '';
        $nested = (isset($options['nested']) && $options['nested']==true) ? true : false;
        $class = (isset($options['class'])) ? $options['class'] : '';
        $permissions = Permission::getAll($this->object->className);
        $label = ($permissions['permissionModify']=='1') ? $this->label(true, $nested) : $this->label(false, $nested);
        $canModify = ($permissions['permissionModify']=='1') ? $this->modify($nested) : '';
        $canDelete = ($permissions['permissionDelete']=='1') ? $this->delete(true) : '';
        $canOrder = ($permissions['permissionModify']=='1' && $this->object->hasOrd()) ? $this->order() : '';
        $relOrd = ($permissions['permissionModify']=='1') ? 'id="'.$this->object->id().'"' : '';
        $viewPublic = ((string)$this->object->info->info->form->viewPublic == 'true') ? $this->view() : '';
        $layout = (string)$this->object->info->info->form->layout;
        $multipleChoice = '';
        if (isset($options['multipleChoice']) && $options['multipleChoice']==true) {
            $multipleChoice .= '<div class="lineAdminCell lineAdminCheckbox">
                                    '.FormField_Checkbox::create(array('name'=>$this->object->id())).'
                                </div>';
        }
        $canOrder = ($canOrder!='') ? '<div class="lineAdminCell lineAdminOrder">'.$canOrder.'</div>' : '';
        return '<div class="lineAdmin'.$this->object->className.' lineAdminLayout'.ucwords($layout).' lineAdmin '.$class.'" '.$relOrd.'>
                    <div class="lineAdminWrapper">
                        '.$multipleChoice.'
                        '.$canOrder.'
                        <div class="lineAdminCell lineAdminLabel">
                            '.$label.'
                        </div>
                        <div class="lineAdminCell lineAdminOptions">
                            '.$viewPublic.'
                            '.$canModify.'
                            '.$canDelete.'
                        </div>
                    </div>
                    <div class="modifySpace"></div>
                </div>';
    }

    /**
    * Render the object as a sitemap url.
    */
    public function renderSitemap($options=array()) {
        $changefreq = isset($options['changefreq']) ? $options['changefreq'] : 'weekly';
        $priority = isset($options['priority']) ? $options['priority'] : '1';
        $xml = '<url>
                    <loc>'.$this->object->url().'</loc>
                    <lastmod>'.date('Y-m-d').'</lastmod>
                    <changefreq>'.$changefreq.'</changefreq>
                    <priority>'.$priority.'</priority>
                </url>';
        return Text::minimize($xml);
    }

    /**
    * Render the object as a sitemap url.
    */
    public function renderRss($options=array()) {
        $xml = ' <item>
                    <title>'.$this->object->getBasicInfo().'</title>
                    <link>'.$this->object->url().'</link>
                    <description><![CDATA['.$this->object->get('description').']]></description>
                </item>';
        return Text::minimize($xml);
    }

    /**
    * Render a form for the object.
    */
    public function renderForm($options=array()) {
        $nested = (isset($options['nested']) && $options['nested']==true) ? true : false;
        $values = (isset($options['values'])) ? $options['values'] : '';
        $action = (isset($options['action'])) ? $options['action'] : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $submit = (isset($options['submit'])) ? $options['submit'] : __('save');
        $formClass = $this->object->className.'_Form';
        $objectForm = new $formClass;
        $form = $objectForm->newArray($values);
        return Form::createForm($form->createFormFields(false, $nested),
                                array('action'=>$action, 'submit'=>$submit, 'class'=>$class, 'nested'=>$nested));
    }

    /**
    * Create a label in the admin using the information in the XML file.
    */
    public function label($canModify=false, $nested=false) {
        if (isset($this->object->info->info->form->templateItemAdmin)) {
            $html = (string)$this->object->info->info->form->templateItemAdmin->asXML();
            $html = str_replace('<templateItemAdmin>', '', $html);
            $html = str_replace('</templateItemAdmin>', '', $html);
            $attributes = Text::arrayWordsStarting('##', $html);
            foreach ($attributes as $attribute) {
                $attribute = str_replace('##', '', $attribute);
                $info = $this->object->attributeInfo($attribute);
                $infoType = (isset($info->type)) ? $info->type : '';
                $labelAttribute = $this->object->get($attribute);
                $html = str_replace('##'.$attribute, $labelAttribute, $html);
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
                    case 'linkid-autoincrement':
                        $refObjectName = (string)$info->refObject;
                        if ($refObjectName!='') {
                            $refObject = new $refObjectName;
                            $refObject = $refObject->readObject($this->object->get($attribute));
                            $labelAttribute = $refObject->getBasicInfoAdmin();
                        }
                    break;
                    case 'textarea-code':
                        $labelAttribute = htmlentities($this->object->get($attribute));
                    break;
                    case 'hidden-login':
                    case 'hidden-user':
                        $user = User::read($this->object->get($attribute));
                        $labelAttribute = ($user->id()!='') ? $user->getBasicInfo() : '';
                    break;
                    case 'select':
                    case 'select-varchar':
                        $labelAttribute = $this->object->label($attribute);
                    break;
                    case 'checkbox':
                        $labelAttribute = ($this->object->get($attribute)=='1') ? __('yes') : __('no');
                    break;
                    case 'date-text':
                        $labelAttribute = Date::sqlText($this->object->get($attribute));
                    break;
                    case 'file':
                        if ((string)$info->mode == 'image') {
                            $labelAttribute = $this->object->getImageIcon($attribute);
                        } else {
                            $labelAttribute = $this->object->getFileLink($attribute);
                        }
                    break;
                }
                $html = str_replace('#'.$attribute, $labelAttribute, $html);
            }
            $wordsTranslate = Text::arrayWordsStarting('_', $html);
            foreach ($wordsTranslate as $wordTranslate) {
                $html = str_replace($wordTranslate, __(str_replace('_', '', $wordTranslate)), $html);
            }
        } else {
            $html = $this->object->getBasicInfoAdmin();
        }
        $html = ($canModify=='1') ? '<a href="'.$this->linkModify($nested).'">'.$html.'</a>' : $html;
        return '<div class="label">'.$html.'</div>';
    }

    /**
    * Render the label text when multiple is active.
    */
    public function labelMultiple($objectName, $objectNameConnector, $separator=', ') {
        $objectNameIns = new $objectName();
        $query = 'SELECT DISTINCT o.*
                    FROM '.Db::prefixTable($objectName).' o
                    JOIN '.Db::prefixTable($objectNameConnector).' bo
                    ON (bo.'.$this->object->primary.'="'.$this->object->id().'" AND bo.'.$objectNameIns->primary.'=o.'.$objectNameIns->primary.')';
        $objects = $objectNameIns->readListQuery($query);
        $html = '';
        foreach ($objects as $object) {
            $html .= $object->getBasicInfo().$separator;
        }
        $html = substr($html, 0, -1 * strlen($separator));
        return $html;
    }

    /**
    * Return the link for modification, in an admin context.
    */
    public function linkModify($nested=false) {
        $link = ($nested) ? 'modifyViewNested' : 'modifyView';
        return url($this->object->className.'/'.$link.'/'.$this->object->id(), true);
    }

    /**
    * Return the link for deletion, in an admin context.
    */
    public function linkDelete($ajax=false) {
        return url($this->object->className.'/'.(($ajax) ? 'deleteAjax' : 'delete').'/'.$this->object->id(), true);
    }

    /**
    * Return a div with the delete link.
    */
    public function delete($ajax=false) {
        return '<div class="iconSide iconDelete '.(($ajax) ? 'iconDeleteAjax' : '').'">
                    <a href="'.$this->linkDelete($ajax).'">
                        <i class="icon icon-trash"></i>
                        <span>'.__('delete').'</span>
                    </a>
                </div>';
    }

    /**
    * Return a div with the modify link.
    */
    public function modify($nested=false) {
        return '<div class="iconSide iconModify">
                    <a href="'.$this->linkModify($nested).'">
                        <i class="icon icon-pencil"></i>
                        <span>'.__('modify').'</span>
                    </a>
                </div>';
    }

    /**
    * Return a div with the view public link.
    */
    public function view() {
        return '<div class="iconSide iconView">
                    <a href="'.$this->object->url().'" target="_blank">
                        <i class="icon icon-view"></i>
                        <span>'.__('view').'</span>
                    </a>
                </div>';
    }

    /**
    * Return a div with the move handle.
    */
    public function order() {
        return '<div class="iconSide iconHandle">
                    <i class="icon icon-move"></i>
                </div>';
    }

    /**
    * Return a div with the share and print elements.
    */
    public function share($options=array()) {
        $title = (isset($options['title'])) ? '<div class="shareOptionsTitle">'.$options['title'].'</div>' : '';
        $facebook = (isset($options['facebook'])) ? '<a href="http://www.facebook.com/sharer/sharer.php?u='.urlencode($this->object->url()).'" target="_blank" class="optionFacebook"><i class="icon icon-facebook"></i><span>Facebook</span></a>' : '';
        $twitter = (isset($options['twitter'])) ? '<a href="http://www.twitter.com/share?text='.urlencode($this->object->getBasicInfo()).'&url='.urlencode($this->object->url()).'" target="_blank" class="optionTwitter"><i class="icon icon-twitter"></i><span>Twitter</span></a>' : '';
        $linkedin = (isset($options['linkedin'])) ? '<a href="https://www.linkedin.com/cws/share?url='.urlencode($this->object->url()).'" target="_blank" class="optionLinkedin"><i class="icon icon-linkedin"></i><span>LinkedIn</span></a>' : '';
        $print = (isset($options['print'])) ? '<a href="javascript:window.print()" class="optionPrint"><i class="icon icon-print"></i>Imprimir</a>' : '';
        return '<div class="shareOptions">
                    '.$title.'
                    <div class="shareOptionsIns">
                        '.$facebook.'
                        '.$twitter.'
                        '.$linkedin.'
                        '.$print.'
                    </div>
                </div>';
    }

}
?>