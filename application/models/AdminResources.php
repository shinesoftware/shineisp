<?php

/**
 * AdminResources
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class AdminResources extends BaseAdminResources
{
	/**
	 * Get all the resources
	 */
	public static function getResources(){
		$aclresources 		= array();
		
		// Get the common resources of ShineISP from the ACL file
		$aclConfig 			= new Zend_Config_Xml(APPLICATION_PATH . '/configs/acl.xml', 'acl');
		
		// Get the custom ACL resources from the database 
		$acldbresources 	= Doctrine_Query::create ()->from ( 'AdminResources r' )->execute(array(), Doctrine::HYDRATE_ARRAY);
		
		foreach ($aclConfig->modules->admin as $controller=>$resources){
			$key = "admin:$controller";
			$aclresources['admin'][$key]['module'] = "admin";
			$aclresources['admin'][$key]['title'] = !empty($resources->title) ? (string)$resources->title : "";
			$aclresources['admin'][$key]['controller'] = $controller;
		}
		
		foreach ($aclConfig->modules->api as $controller=>$resources){
			$key = "api:$controller";
			$aclresources['api'][$key]['module'] = "admin";
			$aclresources['api'][$key]['title'] = !empty($resources->title) ? (string)$resources->title : "";
			$aclresources['api'][$key]['controller'] = $controller;
		}
		
		foreach ($acldbresources as $resource){
			$module = $resource['module'];
			$key = $resource['module'] . ":" . $resource['controller'];
			$aclresources[$module][$key]['module'] = $module;
			$aclresources[$module][$key]['title'] = $resource['name'];
			$aclresources[$module][$key]['controller'] = $resource['controller'];
		}
		
		return $aclresources;
	}
	
	/**
	 * Create a new ACL Resource
	 * 
	 * @param string $module
	 * @param string $controller
	 * @param string $name
	 */
	public static function createResource($module, $controller, $name = null){
		
		if (!empty($module) && !empty($controller)){
			$resource = self::getResource($module, $controller);
			
			if(0 ==  $resource->count()){
				$resource = new AdminResources();
				
				$resource->name = !empty($name) ? $name : $controller;
				$resource->module = $module;
				$resource->controller = $controller;
				if($resource->trySave()){
					return $resource;
				}
			}else{
				return $resource{0};
			}
		}
	}
	
	
	/**
	 * Get the resource record using the module and the controller name
	 * 
	 * @param string $module
	 * @param string $controller
	 * @return Ambigous <Doctrine_Collection, mixed, PDOStatement, Doctrine_Adapter_Statement, Doctrine_Connection_Statement, unknown, number>|NULL
	 */
	public static function getResource($module, $controller){
		
		if (!empty($module) && !empty($controller)){
			$resource = Doctrine_Query::create ()->from ( 'AdminResources r' )
												  ->where('module = ?', $module)
												  ->addWhere('controller = ?', $controller)
												  ->limit(1)
												  ->execute();
			
			return $resource;
		}
		
		return null;
	}
	
	
	
	
	/**
	 * Get all the resources within an array list
	 */
	public static function getList(){
		$data = array();
		$resources = Doctrine_Query::create ()->from ( 'AdminResources r' )
											  ->execute(array(), Doctrine::HYDRATE_ARRAY);
		foreach ($resources as $resource){
			$data[$resource['resource_id']] = $resource['module'] . ":" . $resource['controller'] . " - " . $resource['name'];
		}
		
		return $data;
	}
	
	/**
	 * Get the resources by the parent id
	 * @param integer $id
	 */
	public static function getbyParentId($id) {
		$auth = Zend_Auth::getInstance ();
		$records = Doctrine_Query::create ()->from ( 'AdminResources r' )->where('r.parent_id = ? ', $id)->andWhere('r.admin = ? ', 1);
		
		// Hide some resources within the list
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			if($identity['AdminRoles']['name'] != "administrator"){
				$records->AndWhere('r.hidden = ? ', 0);
			}
		}
		
		return $records->execute(array(), Doctrine::HYDRATE_ARRAY);
	}
	
	/**
	 * Recursive resource grabber
	 * Create the tree resources menu in the administration page
	 *  
	 * @param unknown_type $aclConfig
	 * @param array $selecteditems
	 * @return multitype:boolean NULL multitype:string boolean NULL
	 */
	public static function createResourcesTree($aclConfig, $selecteditems = array(), $module=null) {
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		foreach ($aclConfig as $key => $item){
		    
			if(!empty($item->children)){
			    if($item->ismodule){
			        $module = $key;
			    }
				$res [] = array ('title' => $translator->translate($item->title), 'expand' => true, 'select' => false, 'isFolder' => true, 'children' => self::createResourcesTree($item->children, $selecteditems, $module), 'hideCheckbox' => true );
			}else{
			    $selected = in_array ( "$module:$key", $selecteditems ) ? true : false;
				$res [] = array ('key' => "$module:$key", 'title' => $translator->translate($item->title), 'select' => $selected );
			}
		}
		return $res;
	}
	
}