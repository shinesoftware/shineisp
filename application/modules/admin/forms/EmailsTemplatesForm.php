<?php
class Admin_Form_EmailsTemplatesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('E-Mail Name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
		
    	$this->addElement('select', 'type', array(
            'label'      => $translate->_('Section'),
            'decorators' => array('Bootstrap'),
    		'class'      => 'form-control'
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
            'label'      => $translate->_('From name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

    	$this->addElement('text', 'fromemail', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('From E-Mail'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

    	$this->addElement('text', 'cc', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Always CC'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

    	$this->addElement('text', 'bcc', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Always BCC'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

    	$this->addElement('checkbox', 'plaintext', array(
            'label'      => $translate->_('Always send in plaintext'),
            'decorators' => array('Bootstrap')
        ));

    	$this->addElement('checkbox', 'active', array(
            'label'      => $translate->_('Active'),
            'decorators' => array('Bootstrap')
        ));
		
		$this->addElement('text', 'subject', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('E-Mail Subject'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));		 
		$this->addElement('textarea', 'html', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('HTML Content'),
            'class'      => 'wysiwyg_fullpage'
        ));		 
		$this->addElement('textarea', 'text', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('TEXT Content'),
        ));
		
        $this->addElement('submit', 'save', array(
            'label'    => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
                
        $this->addElement('hidden', 'template_id');
    }
    
}