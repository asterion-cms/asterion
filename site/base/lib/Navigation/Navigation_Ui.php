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
                return '<div class="content_wrapper">
                            <div class="content_top">
                                '.$this->header().'
                            </div>
                            <div class="content">
                                '.$message.'
                                '.$messageError.'
                                <div class="content_ins">
                                    '.$title.'
                                    '.$content.'
                                </div>
                            </div>
                            '.$this->footer().'
                        </div>';
            break;
        }
    }

    public function header() {
        return '<div class="header">
                    <div class="header_ins">
                        <div class="header_left">
                            <div class="logo">
                                <a href="'.url('').'">'.Params::param('metainfo-titlePage').'</a>
                            </div>
                        </div>
                        <div class="header_right">
                            '.Language_Ui::showLanguages(true).'
                        </div>
                    </div>
                </div>';
    }

    public function footer() {
        return '<footer class="footer">
                    <div class="footer_ins">
                        <p>Asterion - <a href="mailto:info@asterion-cms.com">info@asterion-cms.com</a></p>
                        <p>This CMS was invented and is maintained by the [ <a href="http://www.plasticwebs.com/" target="_blank">plasticwebs</a> ] team.</p>
                    </div>
                </footer>';
    }

}
?>