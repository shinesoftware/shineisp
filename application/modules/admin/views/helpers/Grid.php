<?php
/**
 *
 * @version 0.1
 */
/**
 * Admin_View_Helper_Grid helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Grid extends Zend_View_Helper_Abstract {
	
	public function Grid($data) {
		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		if (isset ( $data ['records'] ) && count ( $data ['records'] ) > 0) {
			$this->view->fields = array_keys ( $data ['records'] [0] );
			$this->view->id = (! empty ( $this->view->fields [0] ) && is_numeric ( $data ['records'] [0] [$this->view->fields [0]] )) ? $data ['records'] [0] [$this->view->fields [0]] : "0";
			$this->view->numcols = count ( $this->view->fields );
			$this->view->records = $data ['records'];
			$this->view->currentpage = $data ['currentpage'];
			$this->view->pagination = $data ['pagination'];
			$this->view->customactions = isset ( $data ['customactions'] ) ? $data ['customactions'] : array ();
			$this->view->show_action_box = ! isset ( $data ['show_action_box'] ) ? true : $data ['show_action_box'];
		}
		
		$this->view->recordcount = isset($data ['recordcount']) ? $data ['recordcount'] : 0;
		$this->view->statuses = isset ( $data ['statuses'] ) ? $data ['statuses'] : array ();
		$this->view->filters = isset ( $data ['filters'] ) ? $data ['filters'] : array ();
		return $this->view->render ( 'partials/grid.phtml' );
	}
}
