<?php
class Admin_Form_RegistrarsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$registrars = new Shineisp_Plugins_Registrars_Base();
    	$this->addElement('select', 'name', array(
    			'filters'    => array('StringTrim'),
    			'label'      => $translate->_('Registrar Module'),
    			'required'      => true,
    			'decorators' => array('Composite'),
    			'class'      => 'input-large'
    	));
    	
    	$this->getElement('name')
					    	->setAllowEmpty(false)
					    	->setRegisterInArrayValidator(false)
					    	->setMultiOptions($registrars->getList(true));
    	

        $this->addElement('select', 'active', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Active'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));          
        
        $this->getElement('active')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));

        
        $this->addElement('hidden', 'registrars_id');
    }

    /**
     * Create the custom registrars parameters
     *
     * @param integer $attribute_group_id
     */
    public static function createRegistrarForm($form, $registrar_name) {
    	$config = null;
    	$attributeForm = new Zend_Form_SubForm ();
    	$attributeForm->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    
    	$configfile = PROJECT_PATH . "/library/Shineisp/Plugins/Registrars/". $registrar_name . "/config.xml";
    	if(file_exists($configfile)){
    		$config = simplexml_load_file ( $configfile );
    			
    		foreach ($config->settings->children() as $node) {
    			$arr   = $node->attributes();
    			$var   = strtolower($config['var']) . "_" . (string) $arr['var'];
    			$label = (string) $arr['label'];
    			$type  = (string) $arr['type'];
    			$description = (string) $arr['description'];
    			$default = (string) $arr['default'];
    			$required = (string) $arr['required'];
    				
    			if(!empty($var) && !empty($label) && !empty($type)){
    					
    				// Create the element
    				$attributeForm->addElement ( $type, $var, array ('label' => $label, 'class' => 'input-large', 'decorators' => array('Composite'), 'description' => $description) );
    
    				if ($required) {
    					$attributeForm->getElement ( $var )->setRequired ( true );
    				}
    				
    				// Handle the default option items for the dropdown selector
    				if ($type == "select") {
    					$items = trim((string)$node);
    					$data = ! empty ( $items ) ? json_decode ( $items, true ) : array ();
    					$attributeForm->getElement ( $var )->setAllowEmpty ( false )->setRegisterInArrayValidator ( false )->setMultiOptions ( $data );
    				}
    				
    				if(!empty($default)){
    					$attributeForm->getElement ( $var )->setValue ( $default );
    				}
    			
    				
    				$form->addSubForm ( $attributeForm, 'settings' );
    			}
    		}
    	}
    
    	return array($form, $config);
    }
}