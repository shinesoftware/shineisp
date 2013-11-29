<?php
class Default_Form_TicketsForm extends Zend_Form
{
    
    public function init()
    {
    	$NS = new Zend_Session_Namespace ( 'Default' );
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
       
        $this->addElement('select', 'domain_id', array(
        		'decorators'  => array('Bootstrap'),
        		'label'      => $translate->_('Domain'),
        		'description' => $translate->_('Choose the domain name reference'),
        		'class'       => 'form-control large-input'
        ));
        
        $this->getElement('domain_id')
						        ->setAllowEmpty(false)
						        ->setRegisterInArrayValidator(false)
						        ->setMultiOptions(Domains::getList(true, $NS->customer ['customer_id']));
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'title'       => $translate->_('Write here a subject of the issue.'),
            'label'      => $translate->_('Subject'),
            'description' => $translate->_('Write here the domain name or a simple description of the problem.'),
            'class'       => 'form-control large-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'      => $translate->_('Body Message'),
        	'decorators'  => array('Bootstrap'),
            'description' => $translate->_('Write here all the information.'),
        	'rows'		  => '8',
            'class'       => 'form-control wysiwyg-simple'
        ));
        
        $this->addElement('select', 'status', array(
            'filters'     => array('StringTrim'),
            'label'      => $translate->_('Set the issue status'),
        	'decorators'  => array('Bootstrap'),
            'class'       => 'form-control large-input',
        	'multioptions' => array(''=> '', Statuses::id("solved", "tickets") => $translate->_('Solved'), Statuses::id("closed", "tickets") => $translate->_('Closed'))
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Category'),
            'description' => 'Select a category.',
            'class'       => 'form-control large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(TicketsCategories::getList(true));
                  
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
        	 
        	$MBlimit = Settings::findbyParam('useruploadlimit');
        	$Types = Settings::findbyParam('useruploadfiletypes');
        	
        	if(empty($MBlimit)){
        		$MBlimit = 1;
        	}
        	 
        	if(empty($Types)){
        		$Types = "zip,jpg";
        	}
        	
        	$Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);

			$file = $this->createElement('file', 'attachments', array(
	            'label'          => $translate->_('Attachment'),
	            'description'    => $translate->_('Select the document to upload. Files allowed are (%s) - Max %s', $Types, Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	        ));
	        
	        $file->addValidator ( 'Extension', false, $Types )
				 ->addValidator ( 'Size', false, $Byteslimit ) // 500kb
				 ->addValidator ( 'Count', false, 1 );
	        
			$this->addElement($file);                     
        }else{
        	$this->addElement('hidden', 'attachments');
        }
        
        $this->addElement('submit', 'submit', array(
            'label'      => $translate->_('Send help request'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary btn-lg'
        ));
        
        
        $this->addElement('hidden', 'ticket_id');

    }
    
}