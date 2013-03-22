<?php
/**
 * Cmsheader helper
 */
class Zend_View_Helper_Cmsheader extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	/*
	 * cmsheader
	 * set the data array in this way:
	 * $data['title'] = "My Title";
	 * $data['subitems'][] = array('link'=>'mylink1', 'label'=>'my label 1');
	 * $data['subitems'][] = array('link'=>'mylink2', 'label'=>'my label 2');
	 * $data['subitems'][] = array('link'=>'mylink3', 'label'=>'my label 3');
	 */
	public function cmsheader($data=array()) {
		$this->view->data = $data;
		return $this->view->render ( 'partials/cmsheader.phtml' );
	}
}