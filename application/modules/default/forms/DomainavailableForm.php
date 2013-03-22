<?php
class Default_Form_DomainavailableForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
		$this->addElement('radio', 'options', array(
		    'multiOptions'=>array(
		        'register'=>'Register the domain selected',
		        'donotregister'=>'Do not register this domain',
		        'newdomain'=>'Choose a different domain',
		      ),
		      'decorators'  => array('Composite'),
		  ));
		        
        
        $this->addElement('submit', 'continue', array(
            'required' => false,
            'label'    => 'Continue Order',
            'decorators'  => array('Composite'),
            'class'    => 'button'
        ));

    }
    
}