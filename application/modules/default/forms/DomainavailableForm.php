<?php
class Default_Form_DomainavailableForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
		$this->addElement('radio', 'options', array(
		    'multiOptions'=>array(
		        'register'=> $translate->_('Register the domain selected'),
		        'donotregister'=> $translate->_('Do not register this domain'),
		        'newdomain'=> $translate->_('Choose a different domain'),
		      ),
		      'decorators'  => array('Composite'),
		  ));
		        
        
        $this->addElement('submit', 'continue', array(
            'required' => false,
            'label'      => $translate->_('Continue Order'),
            'decorators'  => array('Composite'),
            'class'    => 'button'
        ));

    }
    
}