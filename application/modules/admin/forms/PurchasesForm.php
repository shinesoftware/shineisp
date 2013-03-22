<?php
class Admin_Form_PurchasesForm extends Zend_Form
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
    	
    	$this->addElement('text', 'expiringdate', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Expiring Date',
            'title'      => 'es: 01/11/2011',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
    	
    	$this->addElement('text', 'paymentdate', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Payment Date',
            'title'      => 'es: 01/11/2010',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('select', 'category_id', array(
            'label'      => 'Category',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(PurchaseCategories::getList());        
        
        $this->addElement('select', 'method_id', array(
            'label'      => 'Payment Method',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('method_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(PaymentsMethods::getList());        

    	$this->addElement('text', 'number', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Number',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Company',
            'required'   => true,
        	'title' => 'es: Google inc.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'total_net', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Total Net',
        	'required'   => true,	
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'total_vat', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Total VAT',
        	'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'total', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Total',
        	'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Note',
            'decorators' => array('Composite'),
            'class'      => 'textarea little-input'
        ));
        
        // If the browser client is an Apple client hide the file upload html object
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
        	
			$file = $this->createElement('file', 'document', array(
	            'label'      => 'Document',
	            'description'      => 'Select the document to upload. Files allowed are (zip,rtf,doc,pdf)',
	            'class'      => 'text-input large-input'
	        ));
	        
	        $file->addValidator ( 'Extension', false, 'zip,rtf,doc,pdf' );
	        $file->setValueDisabled(true);
	        $file->setRequired(false);
			$this->addElement($file);     
	                  
        }
        
        $this->addElement('select', 'status_id', array(
        'label' => 'Status',
        'required' => true,
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders'));
                  		
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
        
        $this->addElement('hidden', 'purchase_id');
    }
}