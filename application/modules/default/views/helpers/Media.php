<?php
/**
 * Media helper
 */
class Zend_View_Helper_Media extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function media() {
		return $this;
	}
	
	/*
	 * ProductImage
	 * Get all the products files attached
	 */
	public function ProductImage($productid, $maxWidth = 100, $maxHeight = 100, $showall=false) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$this->view->width = $maxWidth;
		$this->view->height = $maxHeight;
		$this->view->showall = $showall;
		$this->view->resources = ProductsMedia::getMediabyProductId ( $productid, "*", $NS->langid );
		$this->view->productdata = Products::getAllInfo($productid, $NS->langid );
		return $this->view->render ( 'partials/media.phtml' );
	}
	
}