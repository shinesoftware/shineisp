<?php

/*
 * Crud Action Helper
 */

class Shineisp_Controller_Action_Helper_Datagrid extends Zend_Controller_Action_Helper_Abstract {
	protected $model;
	protected $controller;
	protected $view;
	protected $module;
	protected $config;
	protected $translator;
	protected $redirector;
	protected $session;
	
	public function init() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
	}
	
	/*
	 * preDispatch
	 * Start before the output of the html
	 */
	public function preDispatch() {
		$this->controller = $this->getActionController ();
		$this->view = $this->controller->view;
		if(!empty(Shineisp_Registry::getInstance ()->Zend_Translate)){
			$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		}
		$this->redirector = $this->_actionController->getHelper ( 'Redirector' );
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
	}
	
	/*
	 * Set the module name like customers, orders, invoices, etc...
	 */
	public function setModule($module) {
		$this->module = $module;
		return $this;
	}
	
	/*
	 * Set the data model 
	 */
	public function setModel($model) {
		$this->model = $model;
		return $this;
	}
	
	/*
	 * Set the Config parameter for the datagrid
	 */
	public function setConfig(array $config) {
		$this->config = $config;
		return $this;
	}
	
	/*
	 * datalist
	 * List page: where the grid appears with all the data
	 */
	public function datagrid() {
		$Session 	= new Zend_Session_Namespace ( 'Default' );
		$arrSort 	= array ();
		$filters 	= array ();
		$datagrid 	= $this->config ['datagrid'];
		$request 	= $this->getRequest ();
		$module 	= $this->module;
		$grid 		= new Shineisp_Commons_Datagrid();
		
		$page = $request->getParam ( 'page' );
		$sort = $request->getParam ( 'sort' );
		
		// Default sorting
		foreach($datagrid ['columns'] as $field){
			if(!empty($field['direction'])){
				$sort = $field['field'] . "," . $field['direction'];
			}
		}
		
		if (! empty ( $sort )) {
			$arrSort [] = $this->SortingData ( $sort );
			$arrSort [] = $sort;
		}
		
		if (! empty ( $this->session->$module->filters )) {
			$filters = $this->session->$module->filters['search'];
		}
		
//		Zend_Debug::dump($filters);
//		die();
		
		try {
			
			$id	   			= !empty($datagrid ['id']) ? $datagrid ['id'] : 'itemlist';
			$rows   		= !empty($datagrid ['rownum']) ? $datagrid ['rownum'] : 5;
			$rowNum 		= !empty($this->session->$module->recordsperpage) ? $this->session->$module->recordsperpage : $rows;
			$title 			= !empty($datagrid ['title']) ? $this->translator->translate($datagrid ['title']) : "";
			$page 			= !empty ( $page ) && is_numeric ( $page ) ? $page : 1;
			$filters 		= !empty($filters) ? $this->matchColField($datagrid ['columns'], $filters) : array();
			$massactions 	= !empty($datagrid ['massactions']) ? $datagrid ['massactions'] : array();
			$index 			= !empty($datagrid ['index']) ? $datagrid ['index'] : null;
			$buttons 		= !empty($datagrid ['buttons']) ? $datagrid ['buttons'] : array();
			$statuses 		= !empty($datagrid ['statuses']) ? $datagrid ['statuses'] : array();
			$placeholder	= !empty($datagrid ['placeholder']) ? $datagrid ['placeholder'] : "datagrid";
			$dq 			= !empty($datagrid ['dqrecordset']) && is_object($datagrid['dqrecordset']) ? $datagrid ['dqrecordset'] : null;
			$recordset		= !empty($datagrid ['recordset']) ? $datagrid ['recordset'] : null;
			$rowlist		= !empty($datagrid ['rowlist']) ? $datagrid ['rowlist'] : array ('10', '50', '100', '1000' );
			$hassubrecords 	= !empty($datagrid ['hassubrecords']) ? true : false;
			        
			$mygrid = $grid->setId($id)
							->addColumns ( $datagrid ['columns'] )
							->setStatuses ( $statuses )
							->setHasSubrecords ( $hassubrecords )
							->setMassActions ( $massactions )
							#->addCrudActions ( $buttons, $index )
							->setFilter ( 'searchprocess' )
							->setBasePath ( $datagrid ['basepath'] )
							->setRowlist ( $rowlist )
							->setRownum ( $rowNum )
							->addMultiselect ( $index )
							->setCurrentPage ( $page )
							->setTitle ( $title );
			
			if(!empty($datagrid ['dqrecordset']) && is_object($datagrid['dqrecordset'])){			
				$mygrid->setData ( $dq, $page, $rowNum, $arrSort, $filters );
			}elseif(!empty($recordset) && is_array($recordset)){
				$mygrid->setArrayData ( $recordset, $page, $rowNum, $arrSort, $filters );
			}
								
			$this->view->$placeholder = $mygrid->create();
			
		} catch ( Exception $e ) {
			unset($this->session->$module->filters);
			echo $e->getMessage ();
		}
		
		$this->view->controller = $request->getControllerName(); 
	}
	
	/*
	 * Handle the search operation of the datagrid
	 */
	public function search() {
		$params = array();
		$module = $this->module;
		$request = $this->getRequest ();
		$requested_parameters = array ();
		$columns = $this->config ['datagrid']['columns'];
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		$post = $this->getRequest ()->getParams();
		
		if ($post) {
			$exclude = array('module'=>null, 'controller'=>null, 'action'=>null, 'item'=>null, 'actions'=>null);
			
			// Get only the fields visible in the search form and not all the fields of the form
			foreach ( $post as $index => $parameter ) {
				
				// Exclude some form items
				if (!array_key_exists ( $index, $exclude )) {
					
					// Loop of the datagrid columns
					foreach ($columns as $column){

						// Check if the selected column matches the form item 
						if($column['alias'] == $index){
														
							// Check if the value is not empty
							if (! empty ( $parameter )) {

								// Check the data types
								if($column['type'] == "string"){
									$params ['search'][$index] = array('method' => 'andWhere', 'criteria' => "$index like ?", 'value' => '%' . $parameter . '%');
								}elseif($column['type'] == "date"){

									// Check if the field date value is a range of dates
									if(is_array($parameter) && !empty($parameter[0]) && !empty($parameter[1])){
										// Check if the value is a date
										if(Shineisp_Commons_Utilities::isDate($parameter[0]) && Shineisp_Commons_Utilities::isDate($parameter[1])){
											
											// Convert the date
											$date_1 = Shineisp_Commons_Utilities::formatDateIn($parameter[0]);
											$date_2 = Shineisp_Commons_Utilities::formatDateIn($parameter[1]);
											
											// Build the criteria
											$params ['search'][$index] = array('method' => 'andWhere', 'criteria' => "$index between ? and ?", 'value' => array($date_1, $date_2));
										}
									// Check if the field date value has only one date
									}elseif(is_array($parameter) && !empty($parameter[0]) && empty($parameter[1])){
										
										// Check if the value is a date
										if(Shineisp_Commons_Utilities::isDate($parameter[0])){
											// Convert the date
											$date = Shineisp_Commons_Utilities::formatDateIn($parameter[0]);
											
											// Build the criteria
											$params ['search'][$index] = array('method' => 'andWhere', 'criteria' => "$index = ?", 'value' => $date);
										}
									}
									
								}else{
									$params ['search'][$index] = array('method' => 'andWhere', 'criteria' => "$index = ?", 'value' => $parameter);
								}
								
							}
						}
					}
				}
			}
			
			$this->session->$module->filters = $params;
			
			// Check if it is an ajax request
			if ($this->getRequest ()->isXmlHttpRequest ()) {
				die ( json_encode ( array ('reload' => "/admin/" . $this->module . "/list" ) ) );
			} else {
				$this->redirector->gotoUrl ( "/admin/" . $this->module . "/list" );
			}
		}
	}
	

	/**
	 * SortingData
	 * Manage the request of sort of the records 
	 * @return string
	 */
	private function sortingData($sort) {
		$strSort = "";
		if (! empty ( $sort )) {
			$sort = addslashes ( htmlspecialchars ( $sort ) );
			$sorts = explode ( "-", $sort );
			
			foreach ( $sorts as $sort ) {
				$sort = explode ( ",", $sort );
				$strSort .= $sort [0] . " " . $sort [1] . ",";
			}
			
			if (! empty ( $strSort )) {
				$strSort = substr ( $strSort, 0, - 1 );
			}
		}
		
		return $strSort;
	}
	
	/**
	 * setRowNum
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function setRowNum() {
		$records = $this->getRequest ()->getParam ( 'id' );
		$module = $this->module;
		if (! empty ( $records ) && is_numeric ( $records )) {
			$this->session->$module->recordsperpage = $records ;
		} elseif (! empty ( $records ) && $records == "all") {
			$this->session->$module->recordsperpage = 999999;
		}
		$this->redirector->gotoUrl ( "/admin/" . $this->module . "/list" );
	}	

	/*
	 *  bulkAction
	 *  Execute a custom function for each item selected in the list
	 *  this method will be call from a jQuery script 
	 *  @return string
	 */
	public function massActions() {
		$request = $this->getRequest ();
		$items = $request->getParams ();
		
		if (! empty ( $items ['params'] )) {
			parse_str ( $items ['params'], $arrparams );
			$action = isset ( $arrparams ['do'] ) ? $arrparams ['do'] : "";
			
			if (method_exists ( $this->model, $action )) {
				if(!empty($arrparams['item'])){
					$retval = $this->model->$action ( $arrparams['item'], $arrparams );
					if ($retval) {
						die ( json_encode ( array ('mex' => $this->translator->translate ( "The task requested has been executed successfully." ) ) ) );
					}
				}else{
					die ( json_encode ( array ('mex' => $this->translator->translate ( "No item selected." ) ) ) );	
				}
			} else {
				die ( json_encode ( array ('mex' => $this->translator->translate ( "The action requested has not been developed yet." ) ) ) );
			}
		}
		die ( json_encode ( array ('mex' => $this->translator->translate ( "Unable to process request at this time." ) ) ) );
	}
		
	/*
	 * search
	 * match the html filter fields with the table fields
	 */
	private function matchColField($columns, $search){
		foreach($search as $key=>$item){
			foreach($columns as $column){
				if(!empty($column['alias']) && $column['alias'] == $key){
					$search[$key]['criteria'] = str_replace($key, $column['field'], $search[$key]['criteria']);
				}
			}
		}
		return $search;
	}

	/**
	 * chkFieldType
	 * Check the type of the data and fix the value
	 * @param string $value
	 */
	private function chkFieldType(array $column, $value){
		if($column['type'] == "date"){
			return Shineisp_Commons_Utilities::formatDateIn($value);
		}
		return $value;
	}
}