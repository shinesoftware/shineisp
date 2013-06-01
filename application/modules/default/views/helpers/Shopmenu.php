<?php
/**
 *
 * @version 1.0
 */
/**
 * Shopmenu helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Shopmenu extends Zend_View_Helper_Abstract {
	protected $translator;
	protected $menu = array();
	
	public function shopmenu() {
		$menuheader = "<ul class='navigation'>\n";
		$ns = new Zend_Session_Namespace ();
		$this->translator = Zend_Registry::get ( 'Zend_Translate' );
		#$this->createTldMenu();
		
		$menu = array(
				'items' => array(),
				'parents' => array()
		);
		
		$result = ProductsCategories::getMenu ();
		foreach($result as $menuItem)
		{
			$menu['items'][$menuItem['category_id']] = $menuItem;
			$menu['parents'][$menuItem['parent']][]  = $menuItem['category_id'];
		}
		
		$html = $this->buildMenu(0, $menu);
		$html .= $this->createTldMenu();
		
		// Replace the header of the menu list
		$html = substr_replace($html, $menuheader, 0, strlen("<ul class=''>\n"));
		
		return $html;
	}
	
	private function buildMenu($parentId, $menuData, $deep=0)
	{
		$html = '';
	
		if (isset($menuData['parents'][$parentId]))
		{
			$class = ($deep) ? "dropdown" : "";
			
			$html = "<ul class='$class'>\n";
			foreach ($menuData['parents'][$parentId] as $itemId)
			{
				$html .= '<li><a class="'.$class.'" href="/categories/' . $menuData['items'][$itemId] ['uri'] . '.html">' . $menuData['items'][$itemId]['name'] . "</a>";
				
				$deep++;
				
				// find childitems recursively
				$html .= $this->buildMenu($itemId, $menuData, $deep);
				
				$deep--;
				
				$html .= "</li>\n";
			}
			
			$html .= "</ul>\n";
		}
	
		return $html;
	}
	
	
	/**
	 *
	 * Create the tld list
	 * 
	 */
	private function createTldMenu() {
		$ns = new Zend_Session_Namespace ();
		$items = DomainsTlds::getHighlighted($ns->langid);
		$currency = Zend_Registry::get ( 'Zend_Currency' );
		
		$html = "<ul class=\"navigation\">";
		$html .= "<li>";
		$html .= '<a href="/domainschk/index/" class="dropdown">'.$this->translator->translate('Domains').'</a>';
		$html .= "<ul class=\"dropdown\">";
		foreach ( $items as $item ) {
			if(!empty($item ['DomainsTldsData'][0]['name'])){
				$item ['registration_price'] = $currency->toCurrency($item ['registration_price'], array('currency' => Settings::findbyParam('currency')));
				$html .= '<li><a title="' . Shineisp_Commons_Utilities::truncate(strip_tags($item ['DomainsTldsData'][0]['description']), 150, "...", false, true) . '" href="/tlds/' . $item ['DomainsTldsData'][0]['name'] . '.html">.<b>' . strtoupper($item ['DomainsTldsData'][0]['name']) . "</b> - " . $item ['registration_price'] . " (" . $this->translator->translate('VAT not included') . ")</a></li>";
			}
		}
		$html .= "</ul>";
		$html .= "</ul>";
		
		return $html;
	}

}