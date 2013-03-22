<?php
/**
 *
 * @version 
 */
/**
 * datagrid Buttons helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_grids extends Zend_View_Helper_Abstract {
	
	public function grids() {
		return $this;
	}
	
	public function create($buttons=array()) {
		try {
			$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
			
			// Creation of the datagrid buttons within at the top of the grid.
			$base_buttons ["/admin/$controller/new"] = "new";
			$base_buttons ["/admin/$controller/list"] = "list";
			$base_buttons ["/admin/$controller/search"] = "search";
			$buttons = array_merge($base_buttons, $buttons);
			$this->view->buttons = $buttons;
			return $this->view->render ( 'partials/grids.phtml' );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	
	}

}
