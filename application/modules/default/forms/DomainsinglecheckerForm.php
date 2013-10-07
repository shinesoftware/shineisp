<?php
class Default_Form_DomainsinglecheckerForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'decorators' => array('Composite'),
            'class'       => 'domainame',
            'placeholder'       => 'mycompany',
            'required'   => true
        ));
        
       $this->addElement('select', 'tld', array(
        'decorators' => array('Composite'),
        'class'      => 'tld'
        ));
        
        $this->getElement('tld')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(DomainsTlds::getList(true));      
                          
        $this->addElement('submit', 'check', array(
            'label'    => 'Check',
            'class'    => 'button small chkdomain'
        ));

    }
    
}