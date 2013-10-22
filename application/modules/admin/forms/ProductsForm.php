<?php
class Admin_Form_ProductsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Product name'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
    	
    	$this->addElement('text', 'nickname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Product Nickname'),
            'description'      => $translate->_('This is the short name of the product'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
    	
    	$this->addElement('text', 'uri', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('URI'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

    	$this->addElement('text', 'sku', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('SKU'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('textarea', 'shortdescription', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Short Description'),
            'class'      => 'wysiwyg'
        ));
        
        $this->addElement('textarea', 'metakeywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Keywords'),
            'rows'        => 5,
            'class'       => 'textarea'
        ));     
        
        $this->addElement('textarea', 'metadescription', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Meta Description'),
            'rows'        => 5,
            'class'       => 'textarea'
        ));     
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'class'      => 'wysiwyg'
        ));
        
        $this->addElement('select', 'category_id', array(
        'label' => $translate->_('Category'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ProductsCategories::getList());


        $this->addElement('select', 'welcome_mail_id', array(
        'label' => $translate->_('Welcome E-Mail'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('welcome_mail_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(EmailsTemplates::getList());



        $this->addElement('select', 'server_group_id', array(
        'label' => $translate->_('Server group'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('server_group_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ServersGroups::getList(true));


        $this->addElement('select', 'autosetup', array(
        'label' => $translate->_('Automatic setup'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('autosetup')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(array(
				  	 '0' => 'Do not automatically setup this product'
				  	,'1' => 'Automatically setup the product as soon as an order is placed'
				  	,'2' => 'Automatically setup the product as soon as the first payment is received'
				  	,'3' => 'Automatically setup the product when you manually accept a pending order'
				  	,'4' => 'Automatically setup the product as soon as the payment is complete'
				  ));

        $this->addElement('multiselect', 'related', array(
	        'label' => $translate->_('Related Products'),
	        'decorators' => array('Composite'),
	        'size'	 => '20x',
	        'description'	 => 'Select all the items related to the product selected using the CTRL/SHIFT button',
	        'class'      => 'input-large'
	        ));
		        
        $this->addElement('multiselect', 'upgrade', array(
	        'label' => $translate->_('Product Upgrades'),
	        'decorators' => array('Composite'),
	        'size'	 => '20x',
	        'description'	 => 'Select all the items upgrade to the product selected using the CTRL/SHIFT button',
	        'class'      => 'input-large'
	        ));		
        $this->getElement('related')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(Products::getList());
			
        $this->getElement('upgrade')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(Products::getList());
				  
                  
       $this->addElement('select', 'tax_id', array(
        'label' => 'Tax',
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('tax_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Taxes::getList(true));                  
                  
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Cost'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'price_1', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Price'),
            
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));  
            
        $this->addElement('text', 'setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Setup Fee'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));          
            
        $this->addElement('textarea', 'setup', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Setup'),
            'decorators' => array('Composite'),
            'description'      => $translate->_('XML Setup Configuration. See the manual'),
            'class'      => 'textarea'
        ));          

        $this->addElement('select', 'enabled', array(
            'label'      => $translate->_('Enabled'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'Disabled', '1'=>'Active')
        ));

        $this->addElement('select', 'ishighlighted', array(
            'label'      => $translate->_('Is Highlighted'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'isrefundable', array(
            'label'      => $translate->_('Is Refundable'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));
        
        $this->addElement('select', 'default', array(
            'label'      => $translate->_('Default Image'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'iscomparable', array(
            'label'      => $translate->_('Is Comparable'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'showonrss', array(
            'label'      => $translate->_('Publish on RSS Feed'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'downgradable', array(
            'label'      => $translate->_('Allow downgrades'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

       $this->addElement('select', 'type', array(
        'label' => $translate->_('Product Type'),
        'decorators' => array('Composite'),
        'class'      => 'input-large',
        'multioptions' => array('generic'=> $translate->_('Generic'), 'domain'=> $translate->_('Domain'), 'hosting'=> $translate->_('Hosting'))
        ));        

       // If the browser client is an Apple client hide the file upload html object
       if(false == Shineisp_Commons_Utilities::isAppleClient()){
       	
	        $this->addElement('text', 'filedescription', array(
	            'filters'    => array('StringTrim'),
	            'label'      => $translate->_('Description'),
	            'decorators' => array('Composite'),
	            'class'      => 'input-large'
	        ));
	        
	        
	        $MBlimit = Settings::findbyParam('adminuploadlimit');
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => $translate->_('Attachment'),
	            'description'      => $translate->_('Select the document to upload. Files allowed are (zip,rtf,doc,pdf) - Max %s', Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'class'      => 'input-large'
	        ));
	        
	        $file->addValidator ( 'Extension', false, 'zip,rtf,doc,pdf,png,jpg,gif' )
				 ->addValidator ( 'Size', false, $Byteslimit ) 
				 ->addValidator ( 'Count', false, 1 );
	        
	        $this->addElement($file); 
        }
       
        $this->addElement('multiselect', 'wikipages', array(
        'label' => $translate->_('Wiki Pages'),
        'decorators' => array('Composite'),
        'class'      => 'input-large multiselect'
        ));
        
        $this->getElement('wikipages')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(Wiki::getList());        

        $this->addElement('select', 'tranche_billing_cycle_id', array(
        'label' => $translate->_('Billing Cycle'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('tranche_billing_cycle_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(BillingCycle::getList());        
        
        $this->addElement('text', 'tranche_qty', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Quantity'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));          
        
        $this->addElement('text', 'tranche_measure', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Measurement label'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));          

        $this->addElement('text', 'tranche_setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Setup fee'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));  
		
        $this->addElement('text', 'tranche_price', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Unit Price'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        )); 
                
        $this->addElement('multiselect', 'tranche_includes_domains', array(
        'isArray' => true,
        'label' => $translate->_('Domain included'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('tranche_includes_domains')
                  ->setAllowEmpty(true)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(DomainsTlds::getList());        
        
    	
        $this->addElement('select', 'group_id', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Attribute Group'),
        	'required'    => true,
            'class'       => 'input-large'
        ));
        
        $this->getElement('group_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(ProductsAttributesGroups::getList());
    	
        $this->addElement('text', 'position', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Position'),
            'class'       => 'little-input'
        ));
                          
        $this->addElement('textarea', 'blocks', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Blocks'),
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));        

        $this->addElement('hidden', 'product_id');
    }
}