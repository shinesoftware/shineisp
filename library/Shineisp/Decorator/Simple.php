<?php
class Shineisp_Decorator_Simple extends Zend_Form_Decorator_Abstract {
	
	public function buildLabel() {
		$nolabel = array ();
		$element = $this->getElement ();
		$label = $element->getLabel ();
		if ($translator = $element->getTranslator ()) {
			$label = $translator->translate ( $label );
		}
		if ($element->isRequired ()) {
			$label .= ' ';
		}
		
		$nolabel [] = "Zend_Form_Element_Submit";
		$nolabel [] = "Zend_Form_Element_Reset";
		$nolabel [] = "Zend_Form_Element_Button";
		
		if (! in_array ( $element->getType (), $nolabel )) {
			if (!empty($label)) { // If the label is not an empty value
				return $element->getView ()->formLabel ( $element->getName (), $label );
			}
		}
	}
	
	public function buildInput() {
		$element = $this->getElement ();
		$helper = $element->helper;
		$attrs = $element->getAttribs ();
		$translate = Zend_Registry::get ( 'Zend_Translate' );
		
		if (! isset ( $attrs ['class'] )) {
			$attrs ['class'] = "";
		}
		$required = $element->isRequired () ? "<span title=\"" . $translate->translate ( 'required' ) . "\" class=\"required-icon tooltip\">Required</span>" : "";
		$attrs ['class'] .= $element->hasErrors () ? " inputerror" : "";
		$attrs ['title'] = ! empty ( $attrs ['title'] ) ? $translate->translate ( $attrs ['title'] ) : "";
		
		if ($element->getType () == "Zend_Form_Element_Submit" || $element->getType () == "Zend_Form_Element_Button") {
			$el = $element->getView ()->$helper ( $element->getName (), $translate->translate ( $element->getLabel () ), $attrs, $element->options );
		}else{
			$el = $element->getView ()->$helper ( $element->getName (), $element->getValue (), $attrs, $element->options );
		}
		
		return $el . $required;
	}
	
	public function buildErrors() {
		$element = $this->getElement ();
		$messages = $element->getMessages ();
		if (empty ( $messages )) {
			return '';
		}
		return '<div class="errors">' . $element->getView ()->formErrors ( $messages ) . '</div>';
	}
	
	public function buildDescription() {
		$element = $this->getElement ();
		$desc = $element->getDescription ();
		
		if ($translator = $element->getTranslator ()) {
			$desc = $translator->translate ( $desc );
		}
		
		if (empty ( $desc )) {
			return '';
		}
		return '<br /><small>' . $desc . '</small>';
	}
	
	public function render($content) {
		$element = $this->getElement ();
		if (! $element instanceof Zend_Form_Element) {
			return $content;
		}
		
		if (null === $element->getView ()) {
			return $content;
		}
		
		$separator = $this->getSeparator ();
		$placement = $this->getPlacement ();
		$label = $this->buildLabel ();
		$input = $this->buildInput ();
		$errors = $this->buildErrors ();
		$desc = $this->buildDescription ();
		$name = $this->getElement ()->getName();
		
		// If not the element is a button or a submit button create a div container 
		if($element->getType () != "Zend_Form_Element_Submit" && $element->getType () != "Zend_Form_Element_Button"){
			$output = $label . $input . $desc . $errors;
		}else{
			$output = $input;
		}
		
		switch ($placement) {
			case (self::PREPEND) :
				return $output . $separator . $content;
			
			default :
				return $content . $separator . $output;
		}
	}
}