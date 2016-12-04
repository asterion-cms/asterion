<?php
class Navigation_Ui extends Ui {

    public function render() {
        $layoutPage = (isset($this->object->layoutPage)) ? $this->object->layoutPage : '';
        $title = (isset($this->object->titlePage)) ? '<h1>'.$this->object->titlePage.'</h1>' : '';
        $message = (isset($this->object->message)) ? '<div class="message">'.$this->object->message.'</div>' : '';
        $messageError = (isset($this->object->messageError)) ? '<div class="message messageError">'.$this->object->messageError.'</div>' : '';
        $content = (isset($this->object->content)) ? $this->object->content : '';
        switch ($layoutPage) {
            default:
                return '<div class="contentWrapper">
                            <div class="contentTop">
                                '.$this->header().'
                                '.$this->menu().'
                            </div>
                            <div class="content">
                                '.$message.'
                                '.$messageError.'
                                <div class="contentIns">
                                    <div class="contentInfo">
                                        '.$title.'
                                        '.$content.'
                                    </div>
                                    <div class="contentSidebar">
                                        '.$this->sidebar().'
                                    </div>
                                </div>
                            </div>
                            '.$this->footer().'
                        </div>';
            break;
        }
    }

    public function header() {
        return '<div class="header">
                    <div class="headerIns">
                        <div class="headerLeft">
                            <div class="logo">
                                <a href="'.url('').'">'.Params::param('metainfo-titlePage').'</a>
                            </div>
                        </div>
                        <div class="headerRight">
                            <div class="headerRightTop">
                                '.Lang_Ui::showLangs(true).'
                                '.$this->shareIcons().'
                            </div>
                            <div class="headerRightBottom">
                                '.$this->search().'
                            </div>
                        </div>
                    </div>
                </div>';
    }

    public function footer() {
        return '<footer class="footer">
                    <div class="footerIns">
                        <p>Donec sapien nunc, mollis ut vestibulum eget, malesuada in nisl. Duis suscipit sem vel enim placerat, quis consectetur ligula facilisis. Ut a elit ultricies, ornare nulla quis, pulvinar turpis. </p>
                    </div>
                </footer>';
    }

    public function menu() {
        return '<nav class="menu">
                    <div class="menuIns">
                        <div class="menuItem">
                            <a href="">Menu 1</a>
                        </div>
                        <div class="menuItem">
                            <a href="">Menu 2</a>
                        </div>
                        <div class="menuItem">
                            <a href="">Menu 3</a>
                        </div>
                        <div class="menuItem">
                            <a href="">Menu 4</a>
                        </div>
                    </div>
                </nav>';
    }

    public function sidebar() {
        return '<aside class="sidebar">
                    <div class="sidebarIns">
                        <div class="sidebarBlock">
                            <h2>Sidebar Element</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque et sapien at odio fringilla congue. Nullam rutrum mi eu finibus tincidunt. Curabitur auctor enim diam, in volutpat diam hendrerit id. Mauris auctor eros eu eleifend auctor. Nullam accumsan orci nisl, at egestas tortor scelerisque et.</p>
                        </div>
                        <div class="sidebarBlock">
                            <h2>Sidebar Element</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque et sapien at odio fringilla congue. Nullam rutrum mi eu finibus tincidunt. Curabitur auctor enim diam, in volutpat diam hendrerit id. Mauris auctor eros eu eleifend auctor. Nullam accumsan orci nisl, at egestas tortor scelerisque et.</p>
                        </div>
                    </div>
                </aside>';
    }

    public function shareIcons() {
        $html = '';
        foreach (Params::paramsList() as $code=>$param) {
            if (strpos($code, 'linksocial-')!==false) {
                $code = str_replace('linksocial-', '', $code);
                $html .= '<div class="shareIcon shareIcon-'.$code.'">
                            <a href="'.Url::format($param).'" target="_blank">'.$code.'</a>
                        </div>';
            }
        }
        return ($html!='') ? '<div class="shareIcons">'.$html.'</div>' : '';
    }

    public function search() {
        $field = FormField::create('text', array('name'=>'search', 'placeholder'=>__('search')));
        return '<div class="searchTop">
                    '.Form::createForm($field, array('submit'=>'ajax', 'class'=>'formSearch')).'
                </div>';
    }

}
?>