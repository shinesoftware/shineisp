<?php
/**
 *
 * @version 
 */
/**
 * Menu helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Menu extends Zend_View_Helper_Abstract{
	
	public $view;
	private $translator;
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_View_Helper_Abstract::setView()
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
		$this->translation = Zend_Registry::getInstance ()->Zend_Translate;
	}
	
	/**
	 * webmenu
	 * Create the administrative menu
	 */
	public function menu() {
		
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'admin' ) );
		$identity = $auth->getIdentity();
		
		$data = $this->createMenu(0);

		// Clean up the header and the footer of the list
		$data = substr($data, 0, -5);
		$data = substr($data, 19, strlen($data));
		
		if($identity){
			if(AdminPermissions::isAllowed($identity, "admin", "settings")){
				$configuration = SettingsGroups::getlist ();
				$data .= "<li class=\"item config\">";
				$data .= "<a href=\"\">".$this->translation->translate('Configuration')."</a>";
				$data .= "<ul class=\"subnav\">";
				foreach ($configuration as $id => $item){
					$data .= "<li class=\"item\"><a href=\"/admin/settings/index/groupid/".$id."\">" . $this->translation->translate($item) . "</a></li>";
				}
				$data .= "</li>";
				$data .= "</ul>";
			}
		}
		
		$this->view->basemenu = $data;
		return $this->view->render ( 'partials/menu.phtml' );
	}
	
	/**
	 * createMenu
	 * Create the menu
	 * @param unknown_type $parent
	 */
	private function createMenu($parent) {
		$children = Navigation::getParent ( $parent );
		$items = array ();
		
		if (is_array ( $children )) {
			foreach ( $children as $row ) {
				if($row['parent_id']){
					$link = ! empty ( $row ['url'] ) ? $row ['url'] : "/";
				}else{
					$link = "#";
				}
				$items [] = "<li class=\"item\"><a href=\"" . $link . "\">" . $this->translation->translate($row ['label']) . "</a>" . $this->createMenu ( $row ['id'] ) . "</li>";
			}
		}
		
		if (count ( $items )) {
			return "<ul class=\"subnav\">" . implode ( '', $items ) . "</ul>";
		} else {
			return '';
		}
	}
}