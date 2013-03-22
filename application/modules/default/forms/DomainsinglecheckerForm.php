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
            'class'       => 'text-input medium-input domainame',
            'required'   => true
        ));
        
       $this->addElement('select', 'tld', array(
        'decorators' => array('Composite'),
        'class'      => 'text-input little-input tld'
        ));
        
        $this->getElement('tld')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(DomainsTlds::getList(true));      
                          
        $this->addElement('submit', 'check', array(
            'label'    => 'Check the domain',
            'class'    => 'button chkdomain bigbtn'
        ));

    }
    
}