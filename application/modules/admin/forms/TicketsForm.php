<?php
class Admin_Form_TicketsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Subject'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('text', 'datetime', array(
            'filters'     => array('StringTrim'),
            'label'       => $translate->_('Date'),
        	'decorators'  => array('Bootstrap'),
            'class'       => 'form-control'
        ));
        
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'class'       => 'col-lg-12 form-control wysiwyg'
        ));
        
		$this->addElement('select', 'sendemail', array(
            'label'      => $translate->_('Send Email'),
            'description'      => $translate->_('Send an email to the customer.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control',
            'multioptions' => array('1' => $translate->_('Yes'), '0'=> $translate->_('No'))
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Category'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(TicketsCategories::getList());

        $this->addElement('select', 'customer_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Customer'),
            'class'       => 'form-control'
        ));

        $this->getElement('customer_id')
            ->setAllowEmpty(false)
            ->setRegisterInArrayValidator(false)
            ->setMultiOptions(Customers::getList());

        $this->addElement('select', 'category', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Category'),
            'class'       => 'form-control'
        ));

        $this->getElement('category')
            ->setAllowEmpty(false)
            ->setRegisterInArrayValidator(false)
            ->setMultiOptions(TicketsCategories::getList());

        $this->addElement('select', 'order_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Order reference'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Orders::getList(true));
        
        $this->addElement('select', 'sibling_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Relationships'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('sibling_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false);
        
        $this->addElement('select', 'user_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Operator'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('user_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(AdminUser::getList());
                  #->setMultiOptions(AdminUser::getUserbyRoleID(AdminRoles::getIdRoleByName('operator')));
                  
        $this->addElement('select', 'status_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Status'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Statuses::getList('tickets'));
        
        // If the browser client is an Apple client hide the file upload html object
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
	        	 
	        $MBlimit = Settings::findbyParam('adminuploadlimit');
	        $Types = Settings::findbyParam('adminuploadfiletypes', 'Admin');
	        
	        if(empty($MBlimit)){
	        	$MBlimit = 1;
	        }
	        
	        if(empty($Types)){
	        	$Types = "zip,jpg";
	        }
	        
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => $translate->_('Attachment'),
				'decorators' => array('File', array('ViewScript', array('viewScript' => 'partials/file.phtml', 'placement' => false))),
	            'description'      => $translate->_('Select the document to upload. Files allowed are (%s) - Max %s', $Types, Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'data-classButton' => 'btn btn-primary',
	            'data-input'       => 'false',
	            'class'            => 'filestyle'
	        ));
	        
	        $file->addValidator ( 'Extension', false, $Types )
				 ->addValidator ( 'Size', false, $Byteslimit ) 
				 ->addValidator ( 'Count', false, 1 );
	        
	        $this->addElement($file);
        }else{
        	$this->addElement('hidden', 'attachments');
        }
        
        $this->addElement('hidden', 'ticket_id');

    }
    
}