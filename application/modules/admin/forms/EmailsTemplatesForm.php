<?php
class Admin_Form_EmailsTemplatesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'E-Mail Name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		
    	$this->addElement('select', 'type', array(
            'label'      => 'Section',
            'decorators' => array('Composite')
        ));
        $this->getElement('type')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array(
                    	 'general'    => 'General'
						,'products'   => 'Products'
                    	,'domains'    => 'Domains'
                    	,'supports'   => 'Supports'
                    	,'invoices'   => 'Invoices'
                    	,'affiliates' => 'Affiliates'
                    	,'customers'  => 'Customers'
                    	,'orders'     => 'Orders'
                    )
				);		

    	$this->addElement('text', 'fromname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'From name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('text', 'fromemail', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'From E-Mail',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('text', 'cc', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Always CC',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('text', 'bcc', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Always BCC',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('checkbox', 'plaintext', array(
            'label'      => 'Always send in plaintext',
            'decorators' => array('Composite')
        ));

    	$this->addElement('checkbox', 'active', array(
            'label'      => 'Active',
            'decorators' => array('Composite')
        ));
		
		
		/*
		 * TEXTS
		 */
		$this->addElement('text', 'subject', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'E-Mail Subject',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));		 
		$this->addElement('textarea', 'html', array(
            'filters'    => array('StringTrim'),
            'label'      => 'HTML Content',
            'class'      => 'wysiwyg'
        ));		 
		$this->addElement('textarea', 'text', array(
            'filters'    => array('StringTrim'),
            'label'      => 'TEXT Content',
        ));
		
		
        
        $this->addElement('submit', 'save', array(
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
                
        $this->addElement('hidden', 'template_id');
    }
    
}