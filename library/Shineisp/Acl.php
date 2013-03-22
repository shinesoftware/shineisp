<?php

/**
 * ACL Management
 * @author shinesoftware
 *
 */
class Shineisp_Acl extends Zend_Acl {

	public function __construct() {
		self::roleResource();
	}
	
	/**
	 * Init all the roles 
	 */
	protected function initRoles() {
		$roles = AdminRoles::getRoles();
		
		if(!empty($roles[0])){
// 			echo "> Adding the role: ".$roles[0]['name']."<br/>";
			$this->addRole(new Zend_Acl_Role($roles[0]['name']));
			
			for ($i = 1; $i < count($roles); $i++) {
// 				echo "> Adding the role: ".$roles[$i]['name']. " inherits " . $roles[$i-1]['name']."<br/>";
				$this->addRole(new Zend_Acl_Role($roles[$i]['name']), $roles[$i-1]['name']);
			}
		}
		
	}

	
	/**
	 * Init all the resources 
	 */
	protected function initResources() {
		self::initRoles();
		
		$resources = AdminResources::getResources();
		if(!empty($resources[0])){
			foreach ($resources as $resource){
				$theresource = $resource['module'] . ":" . $resource['controller'];
				if(!empty($resource['action'])){
					$theresource .= ":" . $resource['action'];
				}
	            if (!$this->has($theresource)) {
// 	            	echo "> Adding the resource: $theresource<br/>";
	                $this->add(new Zend_Acl_Resource($theresource));
	            }
	        }
		}
	}

	/**
	 * Check all the resources and roles
	 */
	protected function roleResource()
	{
		self::initResources();
		$acl = AdminRoles::getAll();
		
		foreach ($acl as $data) {
			foreach ($data['AdminPermissions'] as $permission){
				$theresource = $permission['AdminResources']['module'] . ":" . $permission['AdminResources']['controller'];
// 				echo "> " . $permission['permission'] . " " . $data['name'] . " for the $theresource resource <br/>";
				$this->allow($data['name'], $theresource, $permission['permission']);
			}
		}
	}	
}
