<?php
/**
 * @class ParamsController
 *
 * This class is the controller for the Params objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Params_Controller extends Controller
{

    public function listAdmin()
    {
        $html = '';
        $items = (new Params)->readList(['order' => 'code']);
        $paramTypes = ['email', 'metainfo', 'linksocial', 'misc'];
        $infoParamsOrder = [];
        foreach ($paramTypes as $paramType) {
            $infoParamsOrder[$paramType] = [];
        }
        foreach ($items as $item) {
            $type = explode('-', $item->get('code'));
            if (in_array($type[0], $paramTypes)) {
                $infoParamsOrder[$type[0]][] = $item;
            } else {
                $infoParamsOrder['misc'][] = $item;
            }
        }
        foreach ($infoParamsOrder as $infoParamTitle => $infoParamOrder) {
            if (count($infoParamOrder) > 0) {
                $htmlItems = '';
                foreach ($infoParamOrder as $infoParamOrderItem) {
                    $htmlItems .= $infoParamOrderItem->showUi('Admin');
                }
                $html .= '<div class="line_admin_block">
                                <div class="line_admin_title">' . __($infoParamTitle) . '</div>
                                <div class="line_adminItems">
                                    ' . $htmlItems . '
                                </div>
                            </div>';
            }
        }
        return '<div class="line_admin_blockWrapper">' . $html . '</div>';
    }

}
