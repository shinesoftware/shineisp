<?php
class Admin_Form_TicketsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Subject',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'datetime', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'       => 'Date',
        	'decorators'  => array('Composite'),
            'class'       => 'text-input large-input'
        ));
        
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'label'       => 'Reply to the client',
            'class'       => 'wysiwyg'
        ));
        
		$this->addElement('select', 'sendemail', array(
            'label'      => 'Send Email',
            'description'      => 'Send an email to the customer.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('1' => 'Yes', '0'=>'No')
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Category',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(TicketsCategories::getList());
        
        $this->addElement('select', 'sibling_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Relationships',
            'class'       => 'text-input little-input'
        ));
        
        $this->getElement('sibling_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false);
        
        $this->addElement('select', 'user_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Operator',
            'class'       => 'text-input little-input'
        ));
        
        $this->getElement('user_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(AdminUser::getList());
                  #->setMultiOptions(AdminUser::getUserbyRoleID(AdminRoles::getIdRoleByName('operator')));
                  
        $this->addElement('select', 'status_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Status',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Statuses::getList('tickets'));
        
        // If the browser client is an Apple client hide the file upload html object
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
	        	 
	        $MBlimit = Settings::findbyParam('adminuploadlimit', 'admin', Isp::getActiveISPID());
	        $Types = Settings::findbyParam('adminuploadfiletypes', 'admin', Isp::getActiveISPID());
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => 'Attachment',
	            'description'      => Zend_Registry::getInstance ()->Zend_Translate->_('Select the document to upload. Files allowed are (%s) - Max %s', $Types, Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'class'      => 'text-input large-input'
	        ));
	        
	        $file->addValidator ( 'Extension', false, $Types )
				 ->addValidator ( 'Size', false, $Byteslimit ) 
				 ->addValidator ( 'Count', false, 1 );
	        
	        $this->addElement($file);
        }else{
        	$this->addElement('hidden', 'attachments');
        }
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'ticket_id');

    }
    
}