<?php
/**
 * Profile helper
 */
class Admin_View_Helper_Profile extends Zend_View_Helper_Abstract
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function profile()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data = $auth->getIdentity();
            
            $this->view->data = Zend_Registry::get('ISP')->toArray();
        }
        return $this->view->render ( 'partials/profile.phtml' );
    }
}