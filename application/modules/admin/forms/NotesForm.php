<?php
class Admin_Form_NotesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Name'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Description'),
            'class'       => 'input-large span12 wysiwyg'
        ));
        
        $this->addElement('text', 'expire', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Expiry Date'),
            'class'       => 'input-large date'
        ));
        
        $this->addElement('hidden', 'note_id');

    }
    
}