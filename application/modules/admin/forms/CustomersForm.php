<?php
class Admin_Form_CustomersForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Firstname',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Lastname',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'sex', array(
        'label' => 'Sex',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('sex')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('M'=>'Man', 'F'=>'Female'));
        
        $this->addElement('select', 'taxfree', array(
        'label' => 'Tax free',
        'description' => 'If it is set as Yes all the taxes will be not added in the orders',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));

        $this->getElement('taxfree')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));

        $this->addElement('select', 'ignore_latefee', array(
        'label' => 'Ignore late fee',
        'description' => 'If it is set as Yes this customers is not subject to late fee',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));

        $this->getElement('ignore_latefee')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));



        $this->addElement('select', 'language', array(
        'label' => 'Default Language',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('language')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('it_IT'=>'Italiano', 'en_US'=>'English'));

        $this->addElement('select', 'issubscriber', array(
        'label' => 'Newsletter Subscription',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('issubscriber')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));
                  
        $this->addElement('select', 'isreseller', array(
        'label' => 'Is Reseller',
        'description' => 'Set the user as reseller',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('isreseller')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));
        
        $this->addElement('text', 'birthdate', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Birth date',
            'decorators' => array('Composite'),
            'class'        => 'text-input large-input date'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Birth place',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'birthdistrict', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Birth district',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'birthcountry', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Birth country',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'birthnationality', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Birth nationality',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'group_id', array(
        'label' => 'Group',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('group_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(CustomersGroups::getList());
        
        $this->addElement('select', 'type_id', array(
        'label' => 'Company Type',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('type_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform_id', array(
        'label' => 'Legal form',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('legalform_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Legalforms::getList());
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Company Name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('button', 'customerupdate', array(
            'label'    => 'Customer Update',
            'description' => 'Update the customer information retrieving the data from the registrant database.',
            'decorators' => array('Composite'),
            'class'    => 'button red customerupdate'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => 'VAT Number',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
        ));
        
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Taxpayer Identification Number / SSN / TIN',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
        ));
        
         // If the browser client is an Apple client hide the file upload html object  
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
	        $MBlimit = Settings::findbyParam('adminuploadlimit', 'admin', Isp::getActiveISPID());
	        $adminuploadfiletypes = Settings::findbyParam('adminuploadfiletypes', 'admin', Isp::getActiveISPID());
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => 'Attachment',
	            'description'      => 'Select the document to upload. Files allowed are (zip,rtf,doc,pdf) - Max ' . Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit),
	            'class'      => 'text-input large-input'
	        ));
			
			if($adminuploadfiletypes){
				$file->addValidator ( 'Extension', false, $adminuploadfiletypes );
			}
			
			if($Byteslimit){
				$file->addValidator ( 'Size', false, $Byteslimit );
			}
			
	        $file->addValidator ( 'Count', false, 1 );
	        
			$this->addElement($file);          
        
	        $this->addElement('select', 'filecategory', array(
	            'label'      => 'File Category',
	            'decorators' => array('Composite'),
	            'class'      => 'text-input'
	        ));
	        
	        $this->getElement('filecategory')
	                  ->setAllowEmpty(true)
	                  ->setMultiOptions(FilesCategories::getList());
        }
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Address',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Zip code',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'area', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Area',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        
        
        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'label'      => 'City',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'country_id', array(
				        'label' => 'Country',
				        'decorators' => array('Composite'),
				        'class'      => 'text-input large-input')
        );
        $this->getElement('country_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Countries::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'contacttypes', array(
        'label' => 'Contact Types',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'));
        
        $this->getElement('contacttypes')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ContactsTypes::getList());
        
        $this->addElement('text', 'contact', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Contact',
            'decorators' => array('Composite'),
            'class'      => 'text-input medium-input'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Composite'),
            'validators' => array(
                'EmailAddress'
            ),
            'required'   => true,
            'label'      => 'Email',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Private Notes',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input wysiwyg'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Composite'),
            'validators' => array(
                //'Alnum',
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
                //array('StringLength', false, array(6, 20)),
            ),
            'description'      => 'Write here at least 6 characters.',
            'label'      => 'Password',
            'class'      => 'text-input large-input'
        ));
        
        
        $this->addElement('select', 'status_id', array(
        'label' => 'Status',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('customers'));
        
        
        $this->addElement('select', 'parent_id', array(
                            'label' => 'Reseller',
                            'decorators' => array('Composite'),
        					'description' => 'Select the client who you want to join with the selected customer.',
                            'class'      => 'text-input large-input'
        ));
        $criterias = array(array('where'=>'isreseller = ?', 'params'=>'1'));
        $this->getElement('parent_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Customers::getList(true, $criterias));         
                  
        $this->addElement('submit', 'save', array(
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
                
        $this->addElement('hidden', 'customer_id');
    }
}