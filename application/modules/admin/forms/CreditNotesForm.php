<?php
class Admin_Form_CreditNotesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'creationdate', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Date',
            'title'      => 'es: 01/11/2010',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('select', 'invoice_id', array(
            'label'      => 'Invoice',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('invoice_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Invoices::getList());        
        
    	$this->addElement('text', 'number', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Number',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'total_net', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => 'Total Net',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'total_vat', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => 'Total VAT',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'total', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => 'Total',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Note',
            'decorators' => array('Composite'),
            'class'      => 'textarea little-input'
        ));
        
        
        ############################### DETAILS #############################
        
        $this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => 'Quantity',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => 'VAT',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'price', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => 'Price',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'creditnote_id');
    }
}