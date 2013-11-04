<?php

/**
 * BreadCrumb View Helper
 *@author Joey Adams
 *
 */
class Admin_View_Helper_BreadCrumb extends Zend_View_Helper_Abstract{
	
	public function breadCrumb() {
		$registry = Shineisp_Registry::getInstance ();
		$translation = Shineisp_Registry::get ( 'Zend_Translate' );
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
		$homeLink = "<ul class='breadcrumb'><li><a href='{$url}/admin/'><i class='icon-home'></i> Home</a></li>";
		
		// Start crumbs
		$crumbs = $homeLink . " ";
		
		// If our module is default
		if ($l_m == 'default') {
			
			if ($l_a == 'index') {
				$crumbs .= "<li><a href='/admin'><i class='icon-home'></i> " . ucfirst($controller) . "</a></li>";
			} else {
				$crumbs .= "<li><a href='{$url}/{$controller}/'>$controller</a>" . $translation->translate(ucwords($action)) . "</li>";
			}
		} else {
			// Non Default Module
			if ($l_c == 'index' && $l_a == 'index') {
				$crumbs .= "<li>" . ucfirst($module) . "</li>";
			} else {
				$crumbs .= "<li><a href='{$url}/{$module}/'>" . $translation->translate(ucwords($module)) . "</a></li>";
				if ($l_a == 'index') {
					$crumbs .= "<li><a href='/admin'><i class='icon-home'></i> " . $translation->translate(ucwords($controller)) . "</a></li>";
				} else {
					$crumbs .= "<li><a href='{$url}/{$module}/{$controller}/'>" . $translation->translate(ucwords($controller)) . "</a></li>";
				}
			}
		
		}
		$crumbs .= "</ul>";
		return $crumbs;
	}

}
