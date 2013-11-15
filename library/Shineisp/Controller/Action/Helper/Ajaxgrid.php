<?php

/*
 * datagrid Action Helper
 */

class Shineisp_Controller_Action_Helper_Ajaxgrid extends Zend_Controller_Action_Helper_Abstract {
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
		$Session 	= new Zend_Session_Namespace ( 'Admin' );
		$arrSort 	= array ();
		$filters 	= array ();
		$datagrid 	= $this->config ['datagrid'];
		$request 	= $this->getRequest ();
		$module		= $request->getModuleName();
		$controller	= $request->getControllerName();
		$grid 		= new Shineisp_Commons_Ajaxgrid();
		
		try {
			
			$id	   			= !empty($datagrid ['id']) ? $datagrid ['id'] : $controller;
			$jsinject  		= !empty($datagrid ['jsinject']) ? $datagrid ['jsinject'] : "";
			$title 			= !empty($datagrid ['title']) ? $this->translator->translate($datagrid ['title']) : "";
			$massactions 	= !empty($datagrid ['massactions']) ? $datagrid ['massactions'] : array();
			$statuses	 	= !empty($datagrid ['statuses']) ? $datagrid ['statuses'] : array();
			$index 			= !empty($datagrid ['index']) ? $datagrid ['index'] : null;
			$jsoption 		= !empty($datagrid ['jsoption']) ? $datagrid ['jsoption'] : array();
			$rowlist 		= !empty($datagrid ['rowlist']) ? $datagrid ['rowlist'] : array();
			$placeholder	= !empty($datagrid ['placeholder']) ? $datagrid ['placeholder'] : "datagrid";
			$dq 			= !empty($datagrid ['dqrecordset']) && is_object($datagrid['dqrecordset']) ? $datagrid ['dqrecordset'] : null;
			$recordset		= !empty($datagrid ['recordset']) ? $datagrid ['recordset'] : null;
			
			$mygrid = $grid->setId($id)
							->addColumns ( $datagrid ['columns'] )
							->setScriptOptions( $jsoption )
							->setJsinject( $jsinject )
							->setAutoWidth(false)
							->setMassactions($massactions)
							->setStatuses($statuses)
							->addBulkActions()
							->saveStateinCookies()
							->addFooterFilters()
							->showProcessing()
							->setRowsList($rowlist)
							->setPagingType()
							->isServerSide("/$module/$controller/loadrecords")
							->setTitle ( $title );
			
			// Create the main table
			$this->view->$placeholder = $mygrid->create();
			
			// Adding the script 
			$this->view->placeholder ("admin_endbody" )->append ($mygrid->getScript());
			
		} catch ( Exception $e ) {
			unset($this->session->$controller->filters);
			echo $e->getMessage ();
		}
		
		$this->view->controller = $request->getControllerName(); 
	}
	
	/**
	 * Get the records in the data table
	 *
	 * @return json array
	 */
	public function loadRecords($params) {
		$config = $this->config;
		
		$grid = new Shineisp_Commons_Ajaxgrid ();
		$grid->setConfig($config);
		
		echo $grid->loadRecords($params);
		die;
		
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
			$method = isset ( $arrparams ['do'] ) ? $arrparams ['do'] : "";
			
			if (method_exists ( $this->model, $method )) {
				if(!empty($arrparams['item'])){
					$retval = call_user_func(array($this->model, $method), $arrparams['item'], $arrparams);
					if ($retval) {
						die ( json_encode ( array ('mex' => $this->translator->translate ( "The task requested has been executed successfully." ) ) ) );
					}
				}else{
					die ( json_encode ( array ('mex' => $this->translator->translate ( "No item selected." ) ) ) );	
				}
			} else {
				die ( json_encode ( array ('mex' => $this->translator->translate ( "This feature has been not released yet" ) ) ) );
			}
		}
		die ( json_encode ( array ('mex' => $this->translator->translate ( "Unable to process request at this time." ) ) ) );
	}
	
}