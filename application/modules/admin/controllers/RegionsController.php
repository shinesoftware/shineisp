<?php

/**
 * Roles
 * Manage the roles items table
 * @version 1.0
 */

class Admin_RegionsController extends Zend_Controller_Action {
	
	protected $regions;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->roles = new AdminRoles();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "regions" )->setModel ( $this->regions );				
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function getallAction() {
        $result             = array();
        $result['success']  = false;
        
        $id     = $this->getRequest ()->getParam ( 'country_id' );
        $id     = intval( $id ); 
        if ( $id == 0 ) {
            $result['message']  = 'CoutryId not found';
            echo json_encode($result);
            exit();
        }
        
        $result['success']  = true;
        $regions            = Regions::findAll( $id );
        $result['total']    = count($regions);
        if( $result['total'] > 0 ) {
            array_unshift( $regions,array('region_id' => 0, 'name' => '-- Select Regions --') );
        }
        
        $result['rows']     = $regions;
        echo json_encode( $result );
        exit();
	}
	
}
