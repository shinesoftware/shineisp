<?php
/**
 *
 * @version 0.1
 */
/**
 * Blocks helper
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Blocks extends Zend_View_Helper_Abstract {
	
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}

	/**
	 * showblock
	 * Handle the XML Blocks 
	 * @param unknown_type $side
	 */
	public function showblock($side) {
		$ns = new Zend_Session_Namespace ( 'Admin' );
		$languageID = Languages::get_language_id ( $ns->lang );

		$record = array ();
		$blocks = array ();
		
		// Get the main variables
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		
		$customskin = Settings::findbyParam ( 'adminskin' );
		$skin = !empty($customskin) ? $customskin : "base";
		
		// call the placeholder view helper for each side parsed 
		echo $this->view->placeholder ( $side );
		
		// Get all the xml blocks
		$xmlblocks = Shineisp_Commons_Layout::getBlocks ( $module, $side, $controller, $skin );
		
		if (! empty ( $xmlblocks )) {
			$blocks ['reference'] = $xmlblocks;
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
		$ns = new Zend_Session_Namespace ( 'Admin' );
		$languageID = Languages::get_language_id ( $ns->lang );
		
		if (! empty ( $blocks ['side'] )) {
			
			if ($blocks ['side'] == $side) {
				$blocks = $blocks ['block'];
				if (count ( $blocks ) > 1) {
					foreach ( $blocks as $block ) {
						$block = CMSBlocks::findbyvar ( $block ['name'], $languageID );
						if (! empty ( $block [0] ['body'] )) {
							echo $block [0] ['body'];
						}
					}
				} else {
					$block = CMSBlocks::findbyvar ( $blocks ['name'], $languageID );
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