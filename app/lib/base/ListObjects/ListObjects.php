<?php
/**
* @class ListObjects
*
* This is a helper class to create and render lists of objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class ListObjects {

    /**
    * The constructor of the object.
    */
    public function __construct($objectName, $options=array()) {
        $this->objectName = $objectName;
        $this->object = new $objectName();
        $this->options = $options;
        $this->message = (isset($options['message'])) ? $options['message'] : '';
        $this->query = (isset($options['query'])) ? $options['query'] : '';
        $this->queryCount = (isset($options['queryCount'])) ? $options['queryCount'] : '';
        $this->results = (isset($this->options['results']) && $this->options['results']>0) ? intval($this->options['results']) : '';
        $this->populate();
    }

    /**
    * Gets an attribute value.
    */
    public function get($value) {
        return (isset($this->$value)) ? $this->$value : '';
    }

    /**
    * Get the first element of the list.
    */
    public function first() {
        return (count($this->list)>0) ? $this->list[0] : '';
    }

    /**
    * Count the total number of elements in the list.
    */
    public function countTotal() {
        if (!isset($this->countTotal)) {
            if ($this->queryCount!='') {
                $result = Db::returnSingle($this->queryCount);
                $this->countTotal = $result['numElements'];
            } else {
                $this->countTotal = $this->object->countResultsObject($this->options);
            }
        }
        return $this->countTotal;
    }
    
    /**
    * Check if the list is empty.
    */
    public function isEmpty() {
        return (count($this->list)>0) ? false : true;
    }

    /**
    * Populate the list.
    */
    public function populate() {
        $pageUrl = (__('pageUrl')!='pageUrl') ? __('pageUrl') : PAGER_URL_STRING;
        $page = (isset($_GET[$pageUrl])) ? intval($_GET[$pageUrl])-1 : 0;
        if ($this->query!='') {
            if ($this->results!='') {
                $this->options['query'] .= ' LIMIT '.($page*$this->results).', '.$this->results;
            }
            $this->list = $this->object->readListQuery($this->options['query']);
        } else {        
            if ($this->results!='') {
                $this->options['limit'] = ($page*$this->results).', '.$this->results;
            }
            $this->list = $this->object->readListObject($this->options);
        }
    }

    /**
    * Show the list using the Ui object.
    */
    public function showList($options=array(), $params=array()) {
        $sizeList = count($this->list);
        $message = (isset($options['message'])) ? $options['message'] : $this->message;
        $function = (isset($this->options['function'])) ? $this->options['function'] : 'Public';
        $function = (isset($options['function'])) ? $options['function'] : $function;
        $middle = (isset($options['middle'])) ? $options['middle'] : '';
        $middleRepetitions = (isset($options['middleRepetitions'])) ? $options['middleRepetitions'] : 2;
        $html = '';
        if ($sizeList > 0) {
            $middleRepetitions = floor($sizeList/$middleRepetitions)-1;
            $counter = 0;
            foreach($this->list as $item) {
                $itemUiName = $this->objectName.'_Ui';
                $functionName = 'render'.ucwords($function);
                $itemUi = new $itemUiName($item);
                $html .= $itemUi->$functionName($params);
                if ($counter>0 && $middleRepetitions>0 && $middle!='' && $sizeList>$middleRepetitions && $counter%$middleRepetitions==0) {
                    $html .= $middle;
                }
                $counter++;
            }
        } else {
            $html = $message;
        }
        return $html;
    }

    /**
    * Render a pager for the list.
    */
    public function pager($options=array()) {
        if (!isset($this->pagerHtml)) {
            $this->pagerHtml = '';
            $pageUrl = (__('pageUrl')!='pageUrl') ? __('pageUrl') : PAGER_URL_STRING;
            $page = (isset($_GET[$pageUrl])) ? intval($_GET[$pageUrl]) : 0;
            $delta = (isset($options['delta'])) ? intval($options['delta']) : 5;
            $midDelta = ceil($delta/2);
            if ($this->results > 0 && $this->countTotal() > $this->results) {
                $totalPages = ceil($this->countTotal()/$this->results);
                if ($totalPages <= $delta) {
                    //The number of pages is equal or less than delta
                    $listFrom = 0;
                    $listTo = $totalPages - 1;
                    $listStart = false;
                    $listEnd = false;
                } else {
                    if ($page < $midDelta + 1) {
                        //The first pages of the list
                        $listFrom = 0;
                        $listTo = $delta;
                        $listStart = false;
                        $listEnd = true;
                    } else {
                        if ($page+$midDelta >= $totalPages-1) {
                            //The last pages of the list
                            $listFrom = $totalPages - $delta;
                            $listTo = $totalPages - 1;
                            $listStart = true;
                            $listEnd = false;
                        } else {
                            //The middle pages of the list
                            $listFrom = $page - $midDelta;
                            $listTo = $page + $midDelta;
                            $listStart = true;
                            $listEnd = true;                        
                        }                    
                    }
                }
                $html = '';
                for ($i=$listFrom; $i<=$listTo; $i++) {
                    $class = ($i+1==$page) ? 'pagerActive' : 'pager';
                    $class = ($i==0 && $page==0) ? 'pagerActive' : $class;
                    $html .= '<div class="'.$class.'">
                                <a href="'.Url::urlPage($i+1).'">'.($i+1).'</a>
                            </div>';
                }
                $htmlListStart = '';
                if ($listStart) {
                    $htmlListStart = '<div class="pager pagerStart">
                                        <a href="'.Url::urlPage(1).'">1</a>
                                    </div>
                                    <div class="pager pagerStart"><span>...</span></div>';
                };
                $htmlListEnd = '';
                if ($listEnd) {
                    $htmlListEnd = '<div class="pager pagerEnd"><span>...</span></div>
                                    <div class="pager pagerEnd">
                                        <a href="'.Url::urlPage($totalPages).'">'.$totalPages.'</a>
                                    </div>';
                };
                $this->pagerHtml = '<div class="pagerAll">
                                        '.$htmlListStart.'
                                        '.$html.'
                                        '.$htmlListEnd.'
                                    </div>';
            }
        }
        return $this->pagerHtml;
    }

    /**
    * Returns the list with a pager on top and another in the bottom.
    */
    public function showListPager($options=array(), $params=array()) {
        $pager = $this->pager($options);
        $pagerTop = '';
        $pagerBottom = '';
        if ($pager != '') {
            $pagerTop = '<div class="listPagerTop">'.$pager.'</div>';
            $pagerBottom = '<div class="listPagerBottom">'.$pager.'</div>';
        }
        $showResults = (isset($options['showResults'])) ? $options['showResults'] : true;
        $listResults = ($showResults) ? '<div class="listResults">'.str_replace('#RESULTS', $this->countTotal(), __('listTotal')).'</div>' : '';
        return '<div class="listWrapper">
                    '.$pagerTop.'
                    '.$listResults.'
                    <div class="listContent">
                        '.$this->showList($options, $params).'
                    </div>
                    '.$pagerBottom.'
                </div>';
    }

}
?>