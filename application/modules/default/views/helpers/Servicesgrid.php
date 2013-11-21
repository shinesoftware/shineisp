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
class Zend_View_Helper_Servicesgrid extends Zend_View_Helper_Abstract {
	
	public function Servicesgrid($data) {
		
		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		if (isset ( $data ['records'] ) && count ( $data ['records'] ) > 0) {
			
			
			// Create the header using the field name if the columns variable is not set
			if(empty($data ['columns'])){
					
				$data ['columns'] = array();
					
				if(!empty($data ['records'][0])){
					// Get all the fields
					$items = Shineisp_Commons_Utilities::array_flatten($data ['records'][0]);
					$fields = array_keys($items);
					foreach ( $fields as $field ){
						if(strpos($field, "_id")=== false){
							// When a record is called using the HYDRATE_SCALAR mode the table aliases are attached in the field name
							// In this way we delete the first part of the field name. For instance: o_name --> name
							$arrfield = explode("_", $field);
							$field = count($arrfield) > 0 ? $arrfield[count($arrfield)-1] : $field;
							$data ['columns'][] = ucfirst(Shineisp_Registry::getInstance ()->Zend_Translate->translate($field));
						}
					}
				}
			}
			
			$this->view->columns = $data ['columns'];
			$this->view->fields = array_keys ( $data ['records'] [0] );
			$this->view->id = (! empty ( $this->view->fields [0] ) && is_numeric ( $data ['records'] [0] [$this->view->fields [0]] )) ? $data ['records'] [0] [$this->view->fields [0]] : "0";
			$this->view->numcols = count ( $this->view->fields );
			$this->view->records = $data ['records'];
			$this->view->currentpage = $data ['currentpage'];
			$this->view->pagination = $data ['pagination'];
			$this->view->customactions = isset ( $data ['customactions'] ) ? $data ['customactions'] : array ();
			$this->view->show_action_box = ! isset ( $data ['show_action_box'] ) ? true : $data ['show_action_box'];
		}
		
		$this->view->recordcount = $data ['recordcount'];
		$this->view->statuses = isset ( $data ['statuses'] ) ? $data ['statuses'] : array ();
		$this->view->filters = isset ( $data ['filters'] ) ? $data ['filters'] : array ();
		return $this->view->render ( 'partials/servicesgrid.phtml' );
	}
}
