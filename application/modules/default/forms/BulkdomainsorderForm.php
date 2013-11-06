<?php
class Default_Form_BulkdomainsorderForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
         $this->addElement('select', 'billing_id', array(
         'class'      => 'form-control large-input billingId',
         'multiple'   => false
        ));
        
        $this->getElement('billing_id')
                  ->setIsArray(true)
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(null, true)); 
                  
        $this->addElement('submit', 'order', array(
            'label'      => $translate->_('Create the Order'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary bigbtn'
        ));

    }
    
}