<?php
class Admin_Form_InvoicesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'invoice_date', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Date',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('select', 'order_id', array(
            'label'      => 'Order No.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Orders::getList(true));        

    	$this->addElement('select', 'customer_parent_id', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Invoice destination',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'disable'    => 'true'
        ));

        $this->getElement('customer_parent_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Customers::getList(true));

    	$this->addElement('text', 'number', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Sequential number',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));

    	$this->addElement('text', 'formatted_number', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Invoice number',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Private Notes',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input wysiwyg'
        ));
                  
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('reset', 'reset', array(
            'required' => false,
            'label'    => 'reset',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'invoice_id');
    }
}