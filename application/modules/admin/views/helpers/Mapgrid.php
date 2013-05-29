<?php
/**
 *
 * @version 0.1
 */
/**
 * Google Map Grid helper
 * Create a simple address map image list
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Mapgrid extends Zend_View_Helper_Abstract {
	
	public function mapgrid($data) {

		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		if (isset ( $data ['records'] )) {
			// Name of the table, useful for the jQuery pager
			$this->view->name = !empty($data['name']) ? $data['name'] : "table_" . rand(1,50);
			
			// Index of the table 
			$this->view->id = (! empty ( $this->view->fields [0] ) && is_numeric ( $data ['records'] [0] [$this->view->fields [0]] )) ? $data ['records'] [0] [$this->view->fields [0]] : "0";
			
            foreach( $data['records'] as &$record ) {
                $name       = "";
                $regionid   = intval($record['region_id']);
                if( $regionid != 0 ) {
                    $objregion = Regions::find($regionid);
                    $name      = $objregion->name;
                }
                
                $record['region']   = $name;
            }
            unset($record);
            
			// All the records 
			$this->view->records = $data ['records'];
			
			// If these options are true a link appear for each row in a table
			$this->view->edit = ! empty ( $data ['edit'] ) ? $data ['edit'] : false;
			$this->view->delete = ! empty ( $data ['delete'] ) ? $data ['delete'] : false;
			
			// If you need more action use this parameter Array{'url'=>'name'}
			$this->view->actions = ! empty ( $data ['actions'] ) ? $data ['actions'] : false;
			
		}else{
			$this->view->records = "";
		}
		
		// Path of the template
		return $this->view->render ( 'partials/mapgrid.phtml' );
	}
}
