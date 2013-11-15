<?php
class Default_Form_ServicesForm extends Zend_Form
{
    
    public function init()
    {
    	
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('textarea', 'message', array(
            'filters'     => array('StringTrim'),
            'rows'    => 5,
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Message'),
            'description' => $translate->_('Write here your reply. An email will be sent to the ISP staff.'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Auto renewal'),
            'description' => $translate->_('Enable or disable the automatic renewal of the service'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('1'=>$translate->_('Yes, I would like to renew the service at the expiration date.'), '0'=>$translate->_('No, I am not interested in the service renew.')));
				  
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'      => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary'
        ));
		
        $id = $this->addElement('hidden', 'detail_id');

    }
    
}
