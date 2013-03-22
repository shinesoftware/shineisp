<?php
class Default_Form_CallmebackForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'fullname', array(
            'filters'     => array('StringTrim'),
            'decorators' => array('Composite'),
            'title'       => 'Fullname',
            'class'       => 'text-input large-input',
            'required'   => true
        ));
        
        $this->addElement('text', 'telephone', array(
            'filters'     => array('StringTrim'),
            'decorators' => array('Composite'),
        	'title'       => 'Landline phone',
            'class'       => 'text-input large-input',
            'required'   => true
        ));
        
        $captcha = new Zend_Form_Element_Captcha(
        		'captcha', // This is the name of the input field
        		
        		array(
        				'label' => 'Write the chars to the field',
        				'class'       => 'text-input medium-input',
        				'captcha' => array( // Here comes the magic...
        						// First the type...
        						'captcha' => 'Image',
        						// Length of the word...
        						'wordLen' => 3,
        						// Captcha timeout, 5 mins
        						'timeout' => 300,
        						// Dimensions
        						'width' => 80,
        						'height' => 30,
        						'fontsize' =>12,
        						'lineNoiseLevel' =>0,
        						'DotNoiseLevel' =>10,
        						// What font to use...
        						'font' => PUBLIC_PATH . '/resources/fonts/arial.ttf',
        						// Where to put the image
        						'imgDir' => PUBLIC_PATH . '/tmp',
        						// URL to the images
        						// This was bogus, here's how it should be... Sorry again :S
        						'imgUrl' => '/tmp/',
        				)));
        
        $captcha->addDecorator('Label', array('tag' => 'div'));
        $captcha->addDecorator('HtmlTag', array('tag' => 'div'));
        $this->addElement($captcha);
        
        $this->addElement('submit', 'callme', array(
            'label'    => 'Call me',
            'class'    => 'blue-button',
        	'decorators' => array('Composite')
        ));

    }
    
}