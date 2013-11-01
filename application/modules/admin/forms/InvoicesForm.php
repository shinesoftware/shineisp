<?php
class Admin_Form_InvoicesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'invoice_date', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Date'),
            'decorators' => array('Bootstrap'),
            'class'      => 'little-input date'
        ));
        
        $this->addElement('select', 'order_id', array(
            'label'      => $translate->_('Order No.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Orders::getList(true));        

    	$this->addElement('select', 'customer_parent_id', array(
            'label'      => $translate->_('Invoice destination'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control',
            'disable'    => 'true'
        ));

        $this->getElement('customer_parent_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Customers::getList(true));

    	$this->addElement('text', 'number', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Sequential number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'little-input'
        ));

    	$this->addElement('text', 'formatted_number', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Invoice number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'little-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Private Notes'),
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12 form-control wysiwyg'
        ));
        
        $this->addElement('hidden', 'invoice_id');
    }
}