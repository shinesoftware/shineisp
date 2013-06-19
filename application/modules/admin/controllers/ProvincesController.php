<?php

/**
 * Roles
 * Manage the roles items table
 * @version 1.0
 */

class Admin_ProvincesController extends Shineisp_Controller_Admin {
	
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
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
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
        
        $id     = $this->getRequest ()->getParam ( 'region_id' );
        $id     = intval( $id ); 
        if ( $id == 0 ) {
            $result['message']  = 'RegionID not found';
            echo json_encode($result);
            exit();
        }
        
        $result['success']  = true;
        
        $provinces          = Provinces::findAllByRegionID( $id );
        $result['total']    = count($provinces);
        if( $result['total'] > 0 ) {
            array_unshift( $provinces,array('province_id' => 0, 'name' => '-- Select Provinces --','code' => '' ) );
        }
        
        $result['rows']     = $provinces;
        echo json_encode( $result );
        exit();        
	}
	
}
