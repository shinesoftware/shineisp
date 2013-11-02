<?php
class Admin_Form_CreditNotesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'creationdate', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Date'),
            'title'      => $translate->_('eg: 01/11/2010'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg date'
        ));
        
        $this->addElement('select', 'invoice_id', array(
            'label'      => $translate->_('Invoice'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('invoice_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Invoices::getList());        
        
    	$this->addElement('text', 'number', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'total_net', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => $translate->_('Total Net'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'total_vat', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => $translate->_('Total VAT'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'total', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => $translate->_('Total'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Note'),
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12 form-control input-lg'
        ));
        
        
        ############################### DETAILS #############################
        
        $this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => $translate->_('Quantity'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => $translate->_('VAT'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'price', array(
            'filters'    => array('StringTrim', 'LocalizedToNormalized'),
            'label'      => $translate->_('Price'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
        
        $this->addElement('hidden', 'creditnote_id');
    }
}