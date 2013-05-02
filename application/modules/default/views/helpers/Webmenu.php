<?php
/**
 * Webmenu helper
 */
class Zend_View_Helper_Webmenu extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/*
	 * menu
	 * Create the menu
	 */
	public function webmenu() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (!empty($NS->customer)) {
			$this->view->user = $NS->customer;
		}
		$translation = Zend_Registry::getInstance ()->Zend_Translate;
		$locale = $translation->getAdapter ()->getLocale ();
		if(strlen($locale)==2){
			$locale = $locale . "_" . strtoupper($locale);
		}
		
		$this->view->cmslinks = $this->createMenu(0, $locale);
		return $this->view->render ( 'partials/webmenu.phtml' );
	}
	
	private function createMenu($parent, $locale) {
		$children = CmsPages::getParent ( $parent, $locale );
		$items = array ();
		
		if (is_array ( $children )) {
			foreach ( $children as $row ) {
				$link = ! empty ( $row ['link'] ) ? $row ['link'] : "/cms/" . $row ['var'] . ".html";
				$items [] = "<li class=\"item\"><a href=\"" . $link . "\">" . $row ['title'] . "</a>" . $this->createMenu ( $row ['page_id'], $locale ) . "</li>";
			}
		}
		if (count ( $items )) {
			return "<ul class=\"dropdown\">" . implode ( '', $items ) . "</ul>";
		} else {
			return '';
		}
	}
}
