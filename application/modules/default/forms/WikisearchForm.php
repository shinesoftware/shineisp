<?php
class Default_Form_WikisearchForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'topic', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'      => $translate->_('Search'),
            'description' => $translate->_('Write here what you looking for.'),
            'class'       => 'text-input removeqtip large-input'
        ));
        
        $this->addElement('submit', 'wikisearch', array(
            'label'      => $translate->_('Search'),
            'decorators' => array('Composite'),
            'class'    => 'button bigbtn'
        ));
    }
}