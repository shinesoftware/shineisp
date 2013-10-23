<?php
class Admin_Form_CustomersForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Firstname'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Lastname'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('select', 'gender', array(
        'label' => $translate->_('Gender'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('gender')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('M'=>'Man', 'F'=>'Female'));
        
        $this->addElement('select', 'taxfree', array(
        'label' => $translate->_('Tax free'),
        'description' => $translate->_('If it is set as Yes all the taxes will be not added in the orders'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));

        $this->getElement('taxfree')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));

        $this->addElement('select', 'ignore_latefee', array(
        'label' => $translate->_('Ignore late fee'),
        'description' => $translate->_('If it is set as Yes this customers is not subject to late fee'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));

        $this->getElement('ignore_latefee')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));



        $this->addElement('select', 'language_id', array(
        'label' => $translate->_('Default Language'),
        'description' => $translate->_('All the messages sent to the customer will be send using the default language selected'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('language_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Languages::getList());

        $this->addElement('select', 'issubscriber', array(
        'label' => $translate->_('Newsletter Subscription'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('issubscriber')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));
                  
        $this->addElement('select', 'isreseller', array(
        'label' => $translate->_('Is Reseller'),
        'description' => 'Set the user as reseller',
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('isreseller')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No', '1'=>'Yes'));
        
        $this->addElement('text', 'birthdate', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth date'),
            'decorators' => array('Composite'),
            'class'        => 'input-large date'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth place'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'birthdistrict', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth district'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'birthcountry', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth country'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'birthnationality', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth nationality'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('select', 'group_id', array(
        'label' => $translate->_('Group'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('group_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(CustomersGroups::getList());
        
        $this->addElement('select', 'type_id', array(
        'label' => $translate->_('Company Type'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('type_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform_id', array(
        'label' => 'Legal form',
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('legalform_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Legalforms::getList());
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Company Name'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('button', 'customerupdate', array(
            'label'    => 'Customer Update',
            'description' => 'Update the customer information retrieving the data from the registrar database.',
            'decorators' => array('Composite'),
            'class'    => 'button red customerupdate'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('VAT Number'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
        ));
        
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Taxpayer Number'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
        ));
        
         // If the browser client is an Apple client hide the file upload html object  
        if(false == Shineisp_Commons_Utilities::isAppleClient()){
	        $MBlimit = Settings::findbyParam('adminuploadlimit', 'admin', Isp::getActiveISPID());
	        $adminuploadfiletypes = Settings::findbyParam('adminuploadfiletypes', 'admin', Isp::getActiveISPID());
	        $Byteslimit = Shineisp_Commons_Utilities::MB2Bytes($MBlimit);
	        
			$file = $this->createElement('file', 'attachments', array(
	            'label'      => $translate->_('Attachment'),
	            'description'  => $translate->_('Select the document to upload. Files allowed are (zip,rtf,doc,pdf) - Max %s', Shineisp_Commons_Utilities::formatSizeUnits($Byteslimit)),
	            'class'      => 'input-large'
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
	            'label'      => $translate->_('File Category'),
	            'decorators' => array('Composite'),
	            'class'      => 'text-input'
	        ));
	        
	        $this->getElement('filecategory')
	                  ->setAllowEmpty(true)
	                  ->setMultiOptions(FilesCategories::getList());
        }
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Address'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Zip code'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'area', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Area'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));        
        
        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('City'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('select', 'country_id', array(
				        'label' => $translate->_('Country'),
				        'decorators' => array('Composite'),
				        'class'      => 'input-large',
                        'onchange'   => 'onChangeCountry( this );')
        );
        
        
        $this->getElement('country_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Countries::getList( true ))
                  ->setRequired(true);

        $this->addElement('select', 'region_id', array(
                        'label' => $translate->_('Region'),
                        'decorators' => array('Composite'),
                        'class'      => 'input-large',
                        'onchange'   => 'onChangeRegions( this );')
        );
        
        $this->getElement('region_id')
            ->setRegisterInArrayValidator(false)
            ->addValidator( new Shineisp_Validate_Regions( ) );

                  
        $this->addElement('select', 'contacttypes', array(
        'label' => $translate->_('Contact Types'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'));
        
        $this->getElement('contacttypes')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ContactsTypes::getList());
        
        $this->addElement('text', 'contact', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Contact'),
            'decorators' => array('Composite'),
            'class'      => 'medium-input'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Composite'),
            'validators' => array(
                'EmailAddress'
            ),
            'required'   => true,
            'label'      => $translate->_('Email'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Private Notes'),
            'decorators' => array('Composite'),
            'class'      => 'input-large wysiwyg'
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
            'label'      => $translate->_('Password'),
            'class'      => 'input-large'
        ));
        
        
        $this->addElement('select', 'status_id', array(
        'label' => $translate->_('Status'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('customers'));
        
        
        $this->addElement('select', 'parent_id', array(
                            'label' => 'Reseller',
                            'decorators' => array('Composite'),
        					'description' => 'Select the client who you want to join with the selected customer.',
                            'class'      => 'input-large'
        ));
        $criterias = array(array('where'=>'isreseller = ?', 'params'=>'1'));
        $this->getElement('parent_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Customers::getList(true, $criterias));         
                  
        $this->addElement('submit', 'save', array(
            'label'    => $translate->_('Save'),
            'decorators' => array('Composite'),
            'class'    => 'btn'
        ));
                
        $this->addElement('hidden', 'customer_id');
    }
}