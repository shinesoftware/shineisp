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
	   
	   Array
            (
                [data] => Array
                    (
                        [0] => Array
                            (
                                [order_id] => 2212
                                [orderdate] => 16/10/2013
                                [invoice] => 000397-2013
                                [fullname] => Comany Name
                                [total] => € 100,20
                                [grandtotal] => € 122,24
                                [status] => Pagato
                            )
                    )
            
                [index] => order_id
                [fields] => Array
                    (
                        [order_id] => Array
                            (
                                [label] => ID
                                [attributes] => Array
                                    (
                                        [class] => hidden-phone hidden-tablet
                                    )
            
                            )
            
                        [orderdate] => Array
                            (
                                [label] => Data
                                [attributes] => Array
                                    (
                                        [class] => hidden-phone hidden-tablet
                                    )
            
                            )
            
                    )
            
            )
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
		$arrColumns = array();
		$buttons = array();
		$basepath = "";

		if(!empty($records['data']) && is_array($records['data'])){		
			if($type=="grid"){
				$grid = new Shineisp_Commons_Datagrid ();
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
				
				if(!empty($records['fields'])){
					foreach ($records['fields'] as $field => $column) {
						$arrColumns [] = array ('label' => $column['label'], 
												'attributes' => !empty($column['attributes']) ? $column['attributes'] : null, 
												'field' => $field, 
												'alias' => $field, 
												'type' => 'string' );
					}
				}
				
				$keyIndex = !empty($records['index']) ? $records['index'] : null;
				
			    if(!empty($records['data'])){
					$mygrid = $grid->addColumns ( $arrColumns )
									->setCss('table table-striped table-hover')
									->setBasePath($basepath)
									->setRowlist(array())
									->setHiddencols ( $hiddencols )
									->setCurrentPage ( 1 )
									->adddatagridActions ( $buttons, $keyIndex)
									->setArrayData ( $records['data'] );
									
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
