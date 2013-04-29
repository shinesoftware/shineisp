<?php
class Admin_Form_ProductsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Product name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	$this->addElement('text', 'nickname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Product Nickname',
            'description'      => 'This is the short name of the product',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	$this->addElement('text', 'uri', array(
            'filters'    => array('StringTrim'),
            'label'      => 'URI',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('text', 'sku', array(
            'filters'    => array('StringTrim'),
            'label'      => 'SKU',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'shortdescription', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Short Description',
            'class'      => 'wysiwyg'
        ));
        
        $this->addElement('textarea', 'metakeywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Keywords',
            'rows'        => 5,
            'class'       => 'textarea'
        ));     
        
        $this->addElement('textarea', 'metadescription', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Meta Description',
            'rows'        => 5,
            'class'       => 'textarea'
        ));     
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description',
            'class'      => 'wysiwyg'
        ));
        
        $this->addElement('select', 'category_id', array(
        'label' => 'Category',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ProductsCategories::getList());


        $this->addElement('select', 'welcome_mail_id', array(
        'label' => 'Welcome E-Mail',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('welcome_mail_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(EmailsTemplates::getList());



        $this->addElement('select', 'server_group_id', array(
        'label' => 'Server group',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('server_group_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ServersGroups::getList(true));


        $this->addElement('select', 'autosetup', array(
        'label' => 'Automatic setup',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
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
        'label' => 'Related Products',
        'decorators' => array('Composite'),
        'size'	 => '20x',
        'description'	 => 'Select all the items related to the product selected using the CTRL/SHIFT button',
        'class'      => 'text-input large-input'
        ));
		        
        $this->addElement('multiselect', 'upgrade', array(
        'label' => 'Product Upgrades',
        'decorators' => array('Composite'),
        'size'	 => '20x',
        'description'	 => 'Select all the items upgrade to the product selected using the CTRL/SHIFT button',
        'class'      => 'text-input large-input'
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
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('tax_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Taxes::getList(true));                  
                  
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'price_1', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Price',
            
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));  
            
        $this->addElement('text', 'setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Setup Fee',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));          
            
        $this->addElement('textarea', 'setup', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Setup',
            'decorators' => array('Composite'),
            'description'      => 'XML Setup Configuration. See the manual',
            'class'      => 'textarea'
        ));          

        $this->addElement('select', 'enabled', array(
            'label'      => 'Enabled',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('1'=>'Active', '0' => 'Disabled')
        ));

        $this->addElement('select', 'ishighlighted', array(
            'label'      => 'Is Highlighted',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'isrefundable', array(
            'label'      => 'Is Refundable',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));
        
        $this->addElement('select', 'default', array(
            'label'      => 'Default Image',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'iscomparable', array(
            'label'      => 'Is Comparable',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'showonrss', array(
            'label'      => 'Publish on RSS Feed',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

        $this->addElement('select', 'downgradable', array(
            'label'      => 'Allow downgrades',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));

       $this->addElement('select', 'type', array(
        'label' => 'Product Type',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input',
        'multioptions' => array('generic'=>'Generic', 'domain'=>'Domain', 'hosting'=> 'Hosting')
        ));        

       // If the browser client is an Apple client hide the file upload html object
       if(false == Shineisp_Commons_Utilities::isAppleClient()){
       	
	        $this->addElement('text', 'filedescription', array(
	            'filters'    => array('StringTrim'),
	            'label'      => 'Description',
	            'decorators' => array('Composite'),
	            'class'      => 'text-input large-input'
	        ));
	        
	        
	        $MBlimit = Settings::findbyParam('adminuploadlimit', 'admin', Isp::getActiveISPID());
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => 'Attachment',
	            'description'      => 'Select the document to upload. Files allowed are (zip,rtf,doc,pdf) - Max ' . Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit),
	            'class'      => 'text-input large-input'
	        ));
	        
	        $file->addValidator ( 'Extension', false, 'zip,rtf,doc,pdf,png,jpg,gif' )
				 ->addValidator ( 'Size', false, $Byteslimit ) 
				 ->addValidator ( 'Count', false, 1 );
	        
	        $this->addElement($file); 
        }
       
        $this->addElement('multiselect', 'wikipages', array(
        'label' => 'Wiki Pages',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input multiselect'
        ));
        
        $this->getElement('wikipages')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(Wiki::getList());        

        $this->addElement('select', 'tranche_billing_cycle_id', array(
        'label' => 'Billing Cycle',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('tranche_billing_cycle_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(BillingCycle::getList());        
        
        $this->addElement('text', 'tranche_qty', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Quantity',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));          
        
        $this->addElement('text', 'tranche_measure', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Measurement label',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));          

        $this->addElement('text', 'tranche_setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Setup fee',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));  
		
        $this->addElement('text', 'tranche_price', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Unit Price',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        )); 
                
        $this->addElement('multiselect', 'tranche_includes_domains', array(
        'isArray' => true,
        'label' => 'Domain includes',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('tranche_includes_domains')
                  ->setAllowEmpty(true)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(DomainsTlds::getList());        
        
    	
        $this->addElement('select', 'group_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Attribute Group',
        	'required'    => true,
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('group_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(ProductsAttributesGroups::getList());
    	
        $this->addElement('text', 'position', array(
            'decorators'  => array('Composite'),
            'label'       => 'Position',
            'class'       => 'text-input little-input'
        ));
                          
        $this->addElement('textarea', 'blocks', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Blocks',
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));        

        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'product_id');
    }
}