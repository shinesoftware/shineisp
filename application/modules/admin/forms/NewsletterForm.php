<?php
class Admin_Form_NewsletterForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Subject',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'sendat', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Send At',
            'class'       => 'text-input little-input date'
        ));
        
        $this->addElement('text', 'sent', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Sent',
            'class'       => 'text-input little-input date'
        ));
        
        $this->addElement('textarea', 'message', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
        	'required'    => true,
            'label'       => 'Message',
            'class'       => 'textarea wysiwyg'
        ));
   
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('select', 'sendagain', array(
	        'label' => 'Send it again',
	        'decorators' => array('Composite'),
	        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('sendagain')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=>'No, only once', '1'=>'Yes, send again'));        
        
        $this->addElement('hidden', 'news_id');

    }
    
}