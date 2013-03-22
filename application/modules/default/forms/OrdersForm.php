<?php
class Default_Form_OrdersForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Reply',
            'rows'       => '10',
            'description' => 'Write here your reply. An email will be sent to the ISP staff.',
            'class'       => 'textarea wysiwyg'
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $id = $this->addElement('hidden', 'order_id');

    }
    
}