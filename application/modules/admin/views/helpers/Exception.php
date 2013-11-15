<?php
/**
 * Form Errors Exception helper
 * 
 */
class Admin_View_Helper_Exception extends Zend_View_Helper_Abstract{

    /**
     * Create the message box
     * 
     * @param Zend_Form $form 
     * @return string
     */
    public function exception(Zend_Form $form)
    {
        $this->view->form = $form;
        
        return $this->view->render ( 'partials/exception.phtml' );
    }
}