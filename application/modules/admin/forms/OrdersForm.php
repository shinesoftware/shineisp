<?php
class Admin_Form_OrdersForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('select', 'customer_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Customer'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('customer_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Customers::getList(true));
             
    	$this->addElement('select', 'customer_parent_id', array(
            'label'      => $translate->_('Invoice destination'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control',
            'disable'    => 'true'
        ));

        $this->getElement('customer_parent_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Customers::getList(true));

        $this->addElement('select', 'isp_id', array(
            'required'   => true,
            'label'      => $translate->_('ISP'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList());
                  
        $this->addElement('select', 'product_id', array(
            'required'   => false,
            'label'      => $translate->_('Products'),
            'description' => $translate->_('Select the product.'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));
                  
        $this->addElement('select', 'billingcycle_id', array(
            'id'      => 'billingid',
            'label'      => $translate->_('Billing Cycle'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('billingcycle_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(true));
                  
        $this->addElement('select', 'is_renewal', array(
            'label'      => $translate->_('Is a Renewal?'),
            'description' => "If this order is a renewal, it will be checked by ShineISP and it cannot be deleted by the customer in the customer order frontend panel.",
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('is_renewal')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('0' => "No, it's not", '1' => "Yes, it is" ));
                  
        $this->addElement('select', 'invoice_id', array(
            'label'      => $translate->_('Invoice No.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('invoice_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Invoices::getList(true));
                
        $this->addElement('text', 'order_date', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Order Date'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control date'
        ));
                
        $this->addElement('text', 'expiring_date', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Expiry Date'),
            'description'      => 'If this date is set ShineISP will suspend the order at the specified date.',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control date'
        ));
        
        $this->addElement('text', 'date_start', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Date Start'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control date'
        ));
        
        $this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Quantity'),
            'decorators' => array('Bootstrap'),
            'value'         => '1',
            'class'      => 'form-control'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'id'         => 'description',
            'rows'         => '3',
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12 form-control'
        ));

        $this->addElement('text', 'searchdomains', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Searchdomains'),
            'decorators' => array('Bootstrap'),
            'description'      => 'Write here the name of the domain in order to find it in the database.',
            'class'      => 'form-control searchitems'
        ));
        
        $this->addElement('multiselect', 'domains_selected', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Domains Selected'),
            'decorators' => array('Bootstrap'),
            'title'	     => $translate->_('Select ...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick col-md-4'
        ));
        
        $this->getElement('domains_selected')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false);        
                
        $this->addElement('multiselect', 'domains', array(
            'label'      => $translate->_('Domain'),
            'decorators' => array('Bootstrap'),
            'title'	     => $translate->_('Select ...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick col-md-4'
        ));
        
        $this->getElement('domains')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Domains::getList());

                  
        $this->addElement('select', 'referdomain', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Reference Domain'),
            'description' => 'Assign a domain in order toidentify the service/product',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('referdomain')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Domains::getList(true));                  
                          
        $this->addElement('select', 'products', array(
            'label'      => $translate->_('Products'),
            'decorators' => array('Bootstrap'),
            'id'         => 'products',
            'class'      => 'form-control getproducts'
        ));

        // Disable the Validator in order to manage a dynamic products list.
        $this->getElement('products')->setRegisterInArrayValidator(false);
        
        $this->addElement('select', 'categories', array(
	        'label' => $translate->_('Categories'),
	        'decorators'  => array('Bootstrap'),
            'id'          => 'productcategories',
            'class'       => 'form-control',
            'rel'         => 'tree_select'
        ));
        
        $this->getElement('categories')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(ProductsCategories::getList(true))
                  ->setRequired(false);                  
        
        
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Cost'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'price', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Price'),
            'id'         => 'price',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Setup fee'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('VAT'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'total', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Total'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'grandtotal', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Grand Total'),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Save again in order to update the totals.'),
            'class'      => 'form-control bold'
        ));    
            
       $this->addElement('text', 'received_income', array(
       		'readonly'   => 1,
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Income'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));           
       $this->addElement('text', 'missing_income', array(
       		'readonly'   => 1,
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Missing income'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));           
			
        $this->addElement('text', 'fastlink', array(
            'filters'    => array('StringTrim'),
            'id'      => 'fastlink',
            'label'      => $translate->_('Fastlink Code'),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Here you can read a unique code for redirect a user in the order page using the fastlink.'),
            'class'      => 'form-control readonly'
        ));        
            
        $this->addElement('text', 'visits', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Visits'),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Here you can read how many times the order has been viewed by the customer.'),
            'class'      => 'form-control readonly'
        ));        
        
        // If the browser client is an Apple client hide the file upload html object  
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
	        $MBlimit = Settings::findbyParam('adminuploadlimit', 'admin', Isp::getActiveISPID());
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => $translate->_('Attachment'),
				'decorators' => array('File', array('ViewScript', array('viewScript' => 'partials/file.phtml', 'placement' => false))),
	            'description'      => $translate->_('Select the document to upload. Files allowed are (zip,rtf,doc,pdf) - Max %s', Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'data-classButton' => 'btn btn-primary',
	            'data-input'       => 'false',
	            'class'            => 'filestyle'
	        ));
	        
	        $file->addValidator ( 'Extension', false, 'zip,rtf,doc,pdf,png,jpg,gif' )
				 ->addValidator ( 'Size', false, $Byteslimit ) 
				 ->addValidator ( 'Count', false, 1 );
	        
	        $this->addElement($file);
	        
	        $this->addElement('select', 'filecategory', array(
	            'label'      => $translate->_('Category'),
	            'decorators' => array('Bootstrap'),
	            'class'      => 'form-control'
	        ));
	        
	        $this->getElement('filecategory')
	                  ->setAllowEmpty(false)
	                  ->setMultiOptions(FilesCategories::getList())
	                  ->setRegisterInArrayValidator(false)
	                  ->setRequired(false);
        }
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Private Notes'),
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12 form-control wysiwyg'
        ));
        
        $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Post a comment'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control wysiwyg'
        ));        
        
        
        $this->addElement('select', 'status_id', array(
	        'label' => 'Status',
	        'required' => true,
	        'decorators' => array('Bootstrap'),
	        'class'      => 'form-control'
	    ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders'));
        
       $this->addElement('text', 'paymentdate', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Payment date'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control date'
        ));           
                  
       $this->addElement('text', 'reference', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Payment Reference'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));           
        
        $this->addElement('select', 'bank_id', array(
            'id'         => 'paymentmethods',
            'label'      => $translate->_('Bank name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('bank_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Banks::getList(true));                  
                
                  
       $this->addElement('text', 'income', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Income'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));           

        $this->addElement('text', 'payment_description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Notes'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('select', 'confirmed', array(
        		'filters'    => array('StringTrim'),
        		'label'      => $translate->_('Has the Transaction been confirmed?'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control'
        ));
        
        $this->getElement('confirmed')
        ->setAllowEmpty(false)
        ->setMultiOptions(array('0' => "No, not yet", '1' => "Yes, it has been" ));
        
        $this->addElement('hidden', 'order_id');
    }
}