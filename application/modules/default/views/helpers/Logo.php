<?php
/**
 * Logo helper
 */
class Zend_View_Helper_Logo extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function logo($data = array()) {
		$isp = Shineisp_Registry::get('ISP');
		if (! empty ( $isp->logo )) {
			if (file_exists ( PUBLIC_PATH . "/documents/isp/" . $isp->logo )) {
				$this->view->file = "/documents/isp/" . $isp->logo;
			}
		
			$this->view->title  = $isp->company;
			$this->view->slogan = $isp->slogan;
		}
		return $this->view->render ( 'partials/logo.phtml' );
	}
}