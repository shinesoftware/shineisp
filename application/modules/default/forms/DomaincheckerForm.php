<?php
class Default_Form_DomaincheckerForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'domain', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators' => array('Simple'),
            'label'      => 'WWW.',
            'class'       => 'form-control www-input',
            'placeholder'   => 'yourdomain'
        ));
        
        $this->addElement('select', 'tlds', array(
        'label' => $translate->_('Tld'),
        'decorators' => array('Simple'),
        'class'      => 'form-control www-select'
        ));
        
        $this->getElement('tlds')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(DomainsTlds::getList());        
                  
        $this->addElement('submit', 'check', array(
            'label'      => $translate->_('GO')
        ));

    }
    
}