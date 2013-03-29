<?php
class Default_Form_ServicesForm extends Zend_Form
{
    
    public function init()
    {
    	
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('textarea', 'message', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Message',
            'description' => 'Write here your reply. An email will be sent to the ISP staff.',
            'class'       => 'textarea'
        ));
        
        $this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Autorenew',
            'description' => 'Enable or disable the automatic renewal of the service',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('1'=>'Yes, I would like to renew the service at the expiration date.', '0'=>'No, I am not interested in the service renew.'));
				  
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
		
        $id = $this->addElement('hidden', 'detail_id');

    }

	/* JAY - 20130329 - GUEST
	 * Add select to form if there is some upgrade for service
	 ****/
	public function addUpgradeService( $productid ){
		$productForUpgrade	= ProductsUpgrades::getItemsbyProductID($productid);
		if( empty( $productForUpgrade ) ) {
			return;
		}

        $this->addElement('select', 'upgrade', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Upgrade',
            'description' => 'Upgrade your service',
            'class'       => 'text-input large-input'
        ));		
		
		$productsForUpgrade	= array();
		$productsForUpgrade[0]	= 'Select a service';
		foreach( $productForUpgrade as $key => $value ) {
			$productsForUpgrade[$key]	= $value;
		}
		
        $this->getElement('upgrade')
                  ->setAllowEmpty(true)
                  ->setMultiOptions($productsForUpgrade);
		
	}
    
}
