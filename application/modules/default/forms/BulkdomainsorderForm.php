<?php
class Default_Form_BulkdomainsorderForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
         $this->addElement('select', 'billing_id', array(
         'class'      => 'text-input large-input billingId',
         'multiple'   => false
        ));
        
        $this->getElement('billing_id')
                  ->setIsArray(true)
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(null, true)); 
                  
        $this->addElement('submit', 'order', array(
            'label'    => 'Create the Order',
            'decorators' => array('Composite'),
            'class'    => 'button bigbtn'
        ));

    }
    
}