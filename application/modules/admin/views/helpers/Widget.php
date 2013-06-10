<?php
/**
 *
 * @version 0.1
 */
/**
 * Create a simple widget to be add in the dashboard.
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Widget extends Zend_View_Helper_Abstract {
	
	/**
	 * Create a widget object
	 * 
	   $records array with chart
			   =================
	 	Check if the array is a simple or multidimensional data array.
	 	 array(2) {
		  ["data"] => array(4) {
		    [0] => array(2) {
		      ["total"] => string(3) "182"
		      ["column"] => string(6) "Active"
		    }
		    ....
		    ....
		    ....
		  }
		  ["chart"] => string(96) "https://chart.googleapis.com/chart?chs=250x100&chd=t:182,7,1&cht=p3&chl=Active|Deleted|Suspended"
		}
		
		$records array without chart
		===================
		array(4) {
		    [0] => array(2) {
		      ["total"] => string(3) "182"
		      ["column"] => string(6) "Active"
		    }
		    ....
		    ....
		    ....
		  }
	  
	 * @param array $records
	 * @param string $type
	 */
	public function widget($records, $label, $controller="", $hiddencols=array(), $type="grid") {
		
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$buttons = array();
		$basepath = "";
		if(!empty($records) && is_array($records)){		
			if($type=="grid"){
				$grid = new Shineisp_Commons_Datagrid ();
				$data = !empty($records['data']) ? $records['data'] : $records;
				$chart = !empty($records['chart']) ? $records['chart'] : "";
				
				if(!empty($controller)){
					$basepath = "/admin/$controller/";
					$buttons ['edit'] ['label'] = $translator->translate ( 'Edit' );
					$buttons ['edit'] ['cssicon'] = "edit";
					$buttons ['edit'] ['action'] = "/admin/$controller/edit/id/%d";
					$this->view->controller = $basepath;
				}else{
					$this->view->controller = "";
				}
				
				if(!empty($data[0])){
					$columns = array_keys($data[0]);
					foreach ($columns as $column) {
						$column_name = str_replace("_", " ", $column);
						$arrColumns [] = array ('label' => ucwords($column_name), 'field' => $column, 'alias' => $column, 'type' => 'string' );
					}
					
						$mygrid = $grid->addColumns ( $arrColumns )
										->setCss('widget')
										->setBasePath($basepath)
										->setRowlist(array())
										->setHiddencols ( $hiddencols )
										->setCurrentPage ( 1 )
										->adddatagridActions ( $buttons, $columns[0] )
										->setArrayData ( $data );
										
						$this->view->element = $grid->create();
						$this->view->chart = $chart;
					}
				
				$this->view->label = $label;
				return $this->view->render('partials/widget.phtml');
			}else{
				return null;
			}
		}	
	}
}
