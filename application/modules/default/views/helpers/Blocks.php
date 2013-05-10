<?php
/**
 *
 * @version 0.1
 */
/**
 * Blocks helper
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Blocks extends Zend_View_Helper_Abstract {
	
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/*
	 * blocks
	 * handles the blocks on the cms
	 */
	public function getblock($block) {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$languageID = $ns->langid;
		
		$this->view->module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$this->view->controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$this->view->action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		if (! empty ( $block )) {
			$block = CmsBlocks::findbyvar ( $block, $languageID );
			if (! empty ( $block [0] ['body'] )) {
				return $block [0] ['body'];
			}
		}
	}
	
	/**
	 * showblock
	 * Handle the Cms blocks and XML Blocks 
	 * @param unknown_type $side
	 */
	public function showblock($side) {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$languageID = $ns->langid;
		$record = array ();
		$blocks = array ();
		
		// Get the main variables
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		$customskin = Settings::findbyParam ( 'skin' );
		$skin = !empty($customskin) ? $customskin : "base";
		
		// call the placeholder view helper for each side parsed 
		echo $this->view->placeholder ( $side );
		
		// Get all the xml blocks
		$xmlblocks = Shineisp_Commons_Layout::getBlocks ( $module, $side, $controller, $skin );
		
		// HOMEPAGE BLOCKS
		// If the controller called is the homepage, event detail page or a cms page ...
		// #################
		if ($controller == "index") {
			
			$record = CmsPages::findbyvar ( 'homepage', $ns->lang );
			$var = Zend_Controller_Front::getInstance ()->getRequest ()->getParam ( 'url' );
			if (! empty ( $var )) {
				$record = CmsPages::findbyvar ( $var, $ns->lang );
			}
		}
		
		// Load the xml found in the recordset
		if (! empty ( $record ['blocks'] )) {
			// Load the xml into an array
			$xmlobject = simplexml_load_string ( $record ['blocks'] );
			
			// Get the blocks from the xml structure
			if (count ( $xmlobject )) {
				$i = 0;
				
				foreach ( $xmlobject as $block ) {
					$blocks ['reference'] [$i] ['block'] ['name'] = ( string ) $block;
					$blocks ['reference'] [$i] ['side'] = ( string ) $block ['side'];
					$blocks ['reference'] [$i] ['position'] = ( string ) $block ['position'];
					$i ++;
				}
			}
		}
		
		// Join every block in one unique blocks structure
		if (! empty ( $xmlblocks )) {
			if (! empty ( $blocks ['reference'] )) {
				$blocks ['reference'] = array_merge ( $xmlblocks, $blocks ['reference'] );
			} else {
				$blocks ['reference'] = $xmlblocks;
			}
		}
		
		if(!empty($blocks['reference'])){
			$blocks ['reference'] = Shineisp_Commons_Utilities::columnSort ( $blocks['reference'], 'position' );
		}
		
		if (isset ( $blocks ['reference'] ['block'] )) {
			$this->Iterator ( $blocks ['reference'], $side );
		} elseif (isset ( $blocks ['reference'] [0] )) {
			foreach ( $blocks ['reference'] as $block ) {
				$this->Iterator ( $block, $side );
			}
		}
		
		$this->view->module = $module;
		$this->view->controller = $controller;
		$this->view->action = $action;
	
	}
	
	
	
	/**
	 * blocks
	 * Return the blocks
	 */
	public function blocks() {
		return $this;
	}
	
	/**
	 * Iterator
	 * Get all the blocks and attach the content within the view called 
	 * @param array $blocks
	 * @param string $side
	 */
	private function Iterator($blocks, $side) {
		$ns = new Zend_Session_Namespace ( 'Default' );
		
		if (! empty ( $blocks ['side'] )) {
			
			if ($blocks ['side'] == $side) {
				$blocks = $blocks ['block'];
				if (count ( $blocks ) > 1) {
					foreach ( $blocks as $block ) {
						$block = CmsBlocks::findbyvar ( $block ['name'], $ns->langid );
						if (! empty ( $block [0] ['body'] )) {
							echo $block [0] ['body'];
						}
					}
				} else {
					$block = CmsBlocks::findbyvar ( $blocks ['name'], $ns->langid );
					if (! empty ( $block [0] ['body'] )) {
						echo $block [0] ['body'];
					} else {
						echo $blocks ['name'] . " block not found.";
					}
				}
			}
		}
	}
	
	private function sortByOrder($a, $b) {
		return $a ['position'] - $b ['position'];
	}

}