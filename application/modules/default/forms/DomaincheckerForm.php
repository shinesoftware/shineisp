<?php
class Default_Form_DomaincheckerForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'domain', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators' => array('Simple'),
            'label'       => 'WWW.',
            'class'       => 'text-input www-input',
            'placeholder'   => 'yourdomain'
        ));
        
        $this->addElement('select', 'tlds', array(
        'label' => 'Tld',
        'decorators' => array('Simple'),
        'class'      => 'text-input little-input www-select'
        ));
        
        $this->getElement('tlds')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(DomainsTlds::getList());        
                  
        $this->addElement('submit', 'check', array(
            'label'    => 'GO'
        ));

    }
    
}