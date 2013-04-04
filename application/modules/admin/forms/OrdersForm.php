<?php
class Admin_Form_OrdersForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('select', 'customer_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Customer',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('customer_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Customers::getList(true));
                  
    	$this->addElement('select', 'customer_parent_id', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Invoice destination',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'disable'    => 'true'
        ));

        $this->getElement('customer_parent_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Customers::getList(true));

        $this->addElement('select', 'isp_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'ISP',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList());
                  
        $this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Products',
            'description' => 'Select the product.',
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));
                  
        $this->addElement('select', 'billingcycle_id', array(
            'filters'    => array('StringTrim'),
            'id'      => 'billingid',
            'label'      => 'Billing Cycle',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('billingcycle_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(true));
                  
        $this->addElement('select', 'is_renewal', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Is a Renewal?',
            'description' => "If this order is a renewal, it will be checked by ShineISP and it cannot be deleted by the customer in the customer order frontend panel.",
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('is_renewal')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('0' => "No, it's not", '1' => "Yes, it is" ));
                  
        $this->addElement('select', 'invoice_id', array(
            'label'      => 'Invoice No.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('invoice_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Invoices::getList(true));
                
        $this->addElement('text', 'order_date', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Order Date',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input date'
        ));
                
        $this->addElement('text', 'expiring_date', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Expiring Date',
            'description'      => 'If this date is set ShineISP will suspend the order at the specified date.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input date'
        ));
        
        $this->addElement('text', 'date_start', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Date Start',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Quantity',
            'decorators' => array('Composite'),
            'value'         => '1',
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description',
            'id'         => 'description',
            'rows'         => '3',
            'decorators' => array('Composite'),
            'class'      => 'textarea large-input'
        ));

        $this->addElement('text', 'searchdomains', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Searchdomains',
            'decorators' => array('Composite'),
            'description'      => 'Write here the name of the domain in order to find it in the database.',
            'class'      => 'text-input large-input searchitems'
        ));
        
        $this->addElement('multiselect', 'domains_selected', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Domains Selected',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input selecteditems'
        ));
        
        $this->getElement('domains_selected')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false);        
                
        $this->addElement('multiselect', 'domains', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Domain',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input tmpitems'
        ));
        
        $this->getElement('domains')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Domains::getList());

                  
        $this->addElement('select', 'referdomain', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Reference Domain',
            'description' => 'Assign a domain in order toidentify the service/product',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('referdomain')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Domains::getList(true));                  
                          
        $this->addElement('select', 'products', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Products',
            'decorators' => array('Composite'),
            'id'         => 'products',
            'class'      => 'text-input large-input getproducts'
        ));

        // Disable the Validator in order to manage a dynamic products list.
        $this->getElement('products')->setRegisterInArrayValidator(false);
        
        $this->addElement('select', 'categories', array(
	        'label' => 'Categories',
	        'decorators'  => array('Composite'),
            'id'          => 'productcategories',
            'class'       => 'text-input large-input',
            'rel'         => 'tree_select'
        ));
        
        $this->getElement('categories')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(ProductsCategories::getList(true))
                  ->setRequired(false);                  
        
        
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'price', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Price',
            'id'         => 'price',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Setup fee',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => 'VAT',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'total', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Total',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('text', 'grandtotal', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Grand Total',
            'decorators' => array('Composite'),
            'description'      => 'Save again in order to update the totals.',
            'class'      => 'text-input little-input bold'
        ));    
            
       $this->addElement('text', 'received_income', array(
       		'readonly'   => 1,
            'filters'    => array('StringTrim'),
            'label'      => 'Income',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));           
       $this->addElement('text', 'missing_income', array(
       		'readonly'   => 1,
            'filters'    => array('StringTrim'),
            'label'      => 'Missing income',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));           
			
        $this->addElement('text', 'fastlink', array(
            'filters'    => array('StringTrim'),
            'id'      => 'fastlink',
            'label'      => 'Fastlink Code',
            'decorators' => array('Composite'),
            'description'      => 'Here you can read a unique code for redirect a user in the order page using the fastlink.',
            'class'      => 'text-input little-input readonly'
        ));        
            
        $this->addElement('text', 'visits', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Visits',
            'decorators' => array('Composite'),
            'description'      => 'Here you can read how many times the order has been viewed by the customer.',
            'class'      => 'text-input little-input readonly'
        ));        
        
        // If the browser client is an Apple client hide the file upload html object  
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
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
	        
	        $this->addElement('select', 'filecategory', array(
	            'label'      => 'Category',
	            'decorators' => array('Composite'),
	            'class'      => 'text-input'
	        ));
	        
	        $this->getElement('filecategory')
	                  ->setAllowEmpty(false)
	                  ->setMultiOptions(FilesCategories::getList())
	                  ->setRegisterInArrayValidator(false)
	                  ->setRequired(false);
        }
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Private Notes',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input wysiwyg'
        ));
        
        $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Post a comment',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input postcomment wysiwygsimple'
        ));        
        
        
        $this->addElement('select', 'status_id', array(
	        'label' => 'Status',
	        'required' => true,
	        'decorators' => array('Composite'),
	        'class'      => 'text-input large-input'
	    ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders'));
        
       $this->addElement('text', 'paymentdate', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Payment date',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input date'
        ));           
                  
       $this->addElement('text', 'reference', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Payment Reference',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));           
        
        $this->addElement('select', 'bank_id', array(
            'filters'    => array('StringTrim'),
            'id'         => 'paymentmethods',
            'label'      => 'Bank name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('bank_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Banks::getList(true));                  
                
                  
       $this->addElement('text', 'income', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Income',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));           

        $this->addElement('text', 'payment_description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Notes',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'confirmed', array(
        		'filters'    => array('StringTrim'),
        		'label'      => 'Does the Transaction has been confirmed?',
        		'decorators' => array('Composite'),
        		'class'      => 'text-input large-input'
        ));
        
        $this->getElement('confirmed')
        ->setAllowEmpty(false)
        ->setMultiOptions(array('0' => "No, it has been not", '1' => "Yes, it has been" ));
        
        $this->addElement('hidden', 'order_id');
    }
}