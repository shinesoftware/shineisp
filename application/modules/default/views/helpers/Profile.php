<?php
/**
 * Profile helper
 */
class Zend_View_Helper_Profile extends Zend_View_Helper_Abstract
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function profile()
    {
    	$data = array();
    	$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (!empty($NS->customer)) {
            $data = $NS->customer;
        }
        
        if(count($data)>0){
        	$this->view->data = $data;
            $this->view->userlogged = true;
        }else{
        	$this->view->userlogged = false;
        }
        
		$this->view->menu = Navigation::CreateMenu ( Navigation::findAll () );
		
        return $this->view->render ( 'partials/profile.phtml' );
    }
}