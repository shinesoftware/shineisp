<?php
/**
 * Searchbar helper
 */
class Zend_View_Helper_Searchbar extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function searchbar() {
		$form = new Default_Form_SearchForm( array ('action' => '/search/', 'method' => 'post' ));
		$this->view->form = $form;
		return $this->view->render ( 'partials/searchbar.phtml' );
	}
}