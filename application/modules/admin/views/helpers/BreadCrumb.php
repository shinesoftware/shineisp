<?php

/**
 * BreadCrumb View Helper
 *@author Joey Adams
 *
 */
class Admin_View_Helper_BreadCrumb extends Zend_View_Helper_Abstract{
	
	public function breadCrumb() {
		$registry = Zend_Registry::getInstance ();
		$translation = Zend_Registry::get ( 'Zend_Translate' );
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$l_m = strtolower ( $module );
		
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$l_c = strtolower ( $controller );
		
		$action = Zend_Controller_Front::getInstance ()->getRequest ()->getActionName ();
		$l_a = strtolower ( $action );
		
		// HomePage = No Breadcrumb
		if ($l_m == 'default' && $l_c == 'index' && $l_a == 'index') {
			return;
		}
		
		// Get our url and create a home crumb
		$fc = Zend_Controller_Front::getInstance();
		$url = $fc->getBaseUrl();
		$homeLink = "<a href='{$url}/admin/'>Home</a> / ";
		
		// Start crumbs
		$crumbs = $homeLink . " ";
		
		// If our module is default
		if ($l_m == 'default') {
			
			if ($l_a == 'index') {
				$crumbs .= ucfirst($controller);
			} else {
				$crumbs .= "<a href='{$url}/{$controller}/'>$controller</a> / " . $translation->translate(ucwords($action));
			}
		} else {
			// Non Default Module
			if ($l_c == 'index' && $l_a == 'index') {
				$crumbs .= ucfirst($module);
			} else {
				$crumbs .= "<a href='{$url}/{$module}/'>" . $translation->translate(ucwords($module)) . "</a> / ";
				if ($l_a == 'index') {
					$crumbs .= $translation->translate(ucwords($controller));
				} else {
					$crumbs .= "<a href='{$url}/{$module}/{$controller}/'>" . $translation->translate(ucwords($controller)) . "</a> / <a href='#' id='lastbread'>" . $translation->translate(ucwords($action)) ."</a>";
				}
			}
		
		}
		return $crumbs;
	}

}
