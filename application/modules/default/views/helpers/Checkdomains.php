<?php
/**
 * Profile helper
 */
class Zend_View_Helper_Checkdomains extends Zend_View_Helper_Abstract {
    public $view;
    
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }
    
    public function checkdomains() {
        return $this;
    }
    
    public function showForm() {
        $form = new Default_Form_DomainsinglecheckerForm ( array ('action' => '/common/checkdomain', 'method' => 'post' ) );
        $this->view->form = $form;
        return $this->view->render ( 'partials/checkdomains.phtml' );
    }

}