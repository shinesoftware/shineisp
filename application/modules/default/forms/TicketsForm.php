<?php
class Default_Form_TicketsForm extends Zend_Form
{
    
    public function init()
    {
    	$NS = new Zend_Session_Namespace ( 'Default' );
    	
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
       
        $this->addElement('select', 'domain_id', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Domain',
        		'description' => 'Choose the domain name reference',
        		'class'       => 'text-input large-input'
        ));
        
        $this->getElement('domain_id')
						        ->setAllowEmpty(false)
						        ->setRegisterInArrayValidator(false)
						        ->setMultiOptions(Domains::getList(true, $NS->customer ['customer_id']));
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'title'       => 'Write here a subject of the issue.',
            'label'       => 'Subject',
            'description' => 'Write here the domain name or a simple description of the problem.',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'       => 'Body Message',
        	'decorators'  => array('Composite'),
            'description' => 'Write here all the information.',
        	'rows'		  => '8',
            'class'       => 'textarea'
        ));
        
        $this->addElement('select', 'status', array(
            'filters'     => array('StringTrim'),
            'label'       => 'Set the issue status',
        	'decorators'  => array('Composite'),
            'class'       => 'text-input large-input',
        	'multioptions' => array(''=> '', '24' => 'Solved', '25' => 'Closed')
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Category',
            'description' => 'Select a category.',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(TicketsCategories::getList(true));
                  
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
        	 
        	$MBlimit = Settings::findbyParam('useruploadlimit', 'admin', Isp::getCurrentId());
        	$Types = Settings::findbyParam('useruploadfiletypes', 'admin', Isp::getCurrentId());
        	$Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);

			$file = $this->createElement('file', 'attachments', array(
	            'label'      => 'Attachment',
	            'description'      => Zend_Registry::getInstance ()->Zend_Translate->_('Select the document to upload. Files allowed are (%s) - Max %s', $Types, Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'class'      => 'text-input large-input'
	        ));
	        
	        $file->addValidator ( 'Extension', false, $Types )
				 ->addValidator ( 'Size', false, $Byteslimit ) // 500kb
				 ->addValidator ( 'Count', false, 1 );
	        
			$this->addElement($file);                     
        }else{
        	$this->addElement('hidden', 'attachments');
        }
        
        $this->addElement('submit', 'submit', array(
            'label'    => 'Send Request',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        
        $this->addElement('hidden', 'ticket_id');

    }
    
}