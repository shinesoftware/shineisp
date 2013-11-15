<?php
class Default_Form_DomainsinglecheckerForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'class'       => 'domainame form-control',
            'placeholder'       => 'mycompany',
        ));
        
       $this->addElement('select', 'tld', array(
        'decorators' => array('Bootstrap'),
        'class'      => 'tld'
        ));
        
        $this->getElement('tld')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(DomainsTlds::getList(true));      
                          
        $this->addElement('submit', 'check', array(
            'label'      => $translate->_('Check'),
            'class'    => 'btn btn-default chkdomain'
        ));

    }
    
}