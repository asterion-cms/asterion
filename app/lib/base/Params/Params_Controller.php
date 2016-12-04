<?php
/**
* @class ParamsController
*
* This class is the controller for the Params objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Params_Controller extends Controller {
    
	public function listAdmin() {
		$html = '';
		$items = Params::readList(array('order'=>'code'));
		$paramTypes = array('email', 'metainfo', 'linksocial', 'misc');
		$infoParamsOrder = array();
		foreach ($paramTypes as $paramType) {
			$infoParamsOrder[$paramType] = array();
		}
		foreach ($items as $item) {
			$type = explode('-', $item->get('code'));
			if (in_array($type[0], $paramTypes)) {
				$infoParamsOrder[$type[0]][] = $item;
			} else {
				$infoParamsOrder['misc'][] = $item;
			}
		}
		foreach ($infoParamsOrder as $infoParamTitle=>$infoParamOrder) {
			if (count($infoParamOrder) > 0) {
				$htmlItems = '';
				foreach ($infoParamOrder as $infoParamOrderItem) {
					$htmlItems .= $infoParamOrderItem->showUi('Admin');
				}
				$html .= '<div class="lineAdminBlock">
								<div class="lineAdminTitle">'.__($infoParamTitle).'</div>
								<div class="lineAdminItems">
									'.$htmlItems.'
								</div>
							</div>';
			}
		}
		return '<div class="lineAdminBlockWrapper">'.$html.'</div>';
	}

}
?>