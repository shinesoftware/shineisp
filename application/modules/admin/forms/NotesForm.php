<?php
class Admin_Form_NotesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Name',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Description',
            'class'       => 'text-input large-input wysiwyg'
        ));
        
        $this->addElement('text', 'expire', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Expiry Date',
            'class'       => 'text-input large-input date'
        ));
        
        $this->addElement('hidden', 'note_id');

    }
    
}