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
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('select', 'order_id', array(
            'label'      => $translate->_('Order No.'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Orders::getList(true));        

    	$this->addElement('select', 'customer_parent_id', array(
            'label'      => $translate->_('Invoice destination'),
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
            'label'      => $translate->_('Sequential number'),
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));

    	$this->addElement('text', 'formatted_number', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Invoice number'),
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Private Notes'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input wysiwyg'
        ));
        
        $this->addElement('hidden', 'invoice_id');
    }
}