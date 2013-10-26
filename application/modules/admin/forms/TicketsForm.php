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
            'class'       => 'input-large'
        ));
        
        $this->addElement('text', 'datetime', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'       => $translate->_('Date'),
        	'decorators'  => array('Bootstrap'),
            'class'       => 'input-large'
        ));
        
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'label'       => $translate->_('Reply to the client'),
            'class'       => 'wysiwyg'
        ));
        
		$this->addElement('select', 'sendemail', array(
            'label'      => $translate->_('Send Email'),
            'description'      => $translate->_('Send an email to the customer.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large',
            'multioptions' => array('1' => $translate->_('Yes'), '0'=> $translate->_('No'))
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Category'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(TicketsCategories::getList());
        
        $this->addElement('select', 'order_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Order reference'),
            'class'       => 'little-input'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Orders::getList(true));
        
        $this->addElement('select', 'sibling_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Relationships'),
            'class'       => 'little-input'
        ));
        
        $this->getElement('sibling_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false);
        
        $this->addElement('select', 'user_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Operator'),
            'class'       => 'little-input'
        ));
        
        $this->getElement('user_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(AdminUser::getList());
                  #->setMultiOptions(AdminUser::getUserbyRoleID(AdminRoles::getIdRoleByName('operator')));
                  
        $this->addElement('select', 'status_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Status'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Statuses::getList('tickets'));
        
        // If the browser client is an Apple client hide the file upload html object
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
	        	 
	        $MBlimit = Settings::findbyParam('adminuploadlimit');
	        $Types = Settings::findbyParam('adminuploadfiletypes');
	        
	        if(empty($MBlimit)){
	        	$MBlimit = 1;
	        }
	        
	        if(empty($Types)){
	        	$Types = "zip,jpg";
	        }
	        
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => $translate->_('Attachment'),
	            'description'      => $translate->_('Select the document to upload. Files allowed are (%s) - Max %s', $Types, Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'class'      => 'input-large'
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