<?php
/**
 * Webmenu helper
 */
class Zend_View_Helper_Webmenu extends Zend_View_Helper_Abstract {
	public $view;
	protected $translator;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/**
	 * menu
	 * Create the menu
	 * @param string $menucls - main CSS class
	 */
	public function webmenu($menucls="") {
		$this->translator = Shineisp_Registry::get ( 'Zend_Translate' );
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (!empty($NS->customer)) {
			$this->view->user = $NS->customer;
		}
		
		// Get the store categories product
		$categories = ProductsCategories::getMenu ();
		$storecategories = $this->storeCategoriesMenu($categories, 0 );
		
		// Create CMS pages menu
		$cmsmenu = '<li class="dropdown"><a data-toggle="dropdown" href="">'.$this->translator->translate('Blog').'</a>' . $this->createMenu(0) . "</li>";
		
		// Create the tlds list menu
		$tldmenu = $this->createTldMenu();
		
		// Delete the last </ul> in the list
		$storecategories = substr_replace($storecategories , "", -5);
		
		// Replace the header of the menu list
		$storecategories = substr_replace($storecategories, $tldmenu . $cmsmenu, 0, strlen("<ul class=''>"));
		
		// we will send the body of the bullet list to the partial template
		$this->view->menu = $storecategories;
		$this->view->menucls = $menucls;
		return $this->view->render ( 'partials/webmenu.phtml' );
	}
	
	/**
	 * Build a recursive store categories menu
	 * @param array $categories
	 * @param integer $parent
	 * @param integer $level
	 * @return string
	 */
	function storeCategoriesMenu($categories, $parent = null, $level = 0)
	{
		$class = ($level) ? "dropdown-menu" : "";
		
	    $ret = "<ul class='$class'>";
	    foreach($categories as $index => $category)
	    {
	        if($category['parent'] == $parent)
	        {
	            if($category['children'] > 0){
	            	$ret .= "<li class=\"dropdown\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">" . $category['name'] . "</a>";
	                $ret .= $this->storeCategoriesMenu($categories, $category['id'], $level+1);
	            }else{
	            	$hasdropdown = $level==0 ? "dropdown" : "";
	            	$ret .= "<li class=\"$hasdropdown\"><a href=\"/categories/" . $category['uri'] . ".html\">" . $category['name'] . "</a>";
	            }
	            $ret .= '</li>';
	        }
	    }
	    return $ret . '</ul>';
	}
	
	/** 
	 * Create a CMS menu
	 * @param unknown_type $parent
	 * @param unknown_type $locale
	 * @return string
	 */
	private function createMenu($parent) {
		
		$locale = $this->translator->getAdapter ()->getLocale ();
		
		$children = CmsPages::getParent ( $parent, $locale );
		$items = array ();
		
		if (is_array ( $children )) {
			foreach ( $children as $row ) {
				$link = ! empty ( $row ['link'] ) ? $row ['link'] : "/cms/" . $row ['var'] . ".html";
				$items [] = "<li class=\"dropdown\"><a href=\"" . $link . "\">" . $row ['title'] . "</a>" . $this->createMenu ( $row ['page_id'] ) . "</li>";
			}
		}
		if (count ( $items )) {
			return "<ul class=\"dropdown-menu\">" . implode ( '', $items ) . "</ul>";
		} else {
			return '';
		}
	}
	
	/**
	 * Create the tld list
	 */
	private function createTldMenu() {
		$ns = new Zend_Session_Namespace ();
		$items = DomainsTlds::getHighlighted($ns->langid);
		$currency = Shineisp_Registry::get ( 'Zend_Currency' );
		$html = "";
		
		if(!empty($items)){
    		$html = "<li class=\"dropdown\">";
    		$html .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">'.$this->translator->translate('Domains').'</a>';
    		$html .= "<ul class=\"dropdown-menu\">";
    		foreach ( $items as $item ) {
    			if(!empty($item ['DomainsTldsData'][0]['name'])){
    				$item ['registration_price'] = $currency->toCurrency($item ['registration_price'], array('currency' => Settings::findbyParam('currency')));
    				$html .= '<li class="dropdown"><a title="' . Shineisp_Commons_Utilities::truncate(strip_tags($item ['DomainsTldsData'][0]['description']), 150, "...", false, true) . '" href="/tlds/' . $item ['DomainsTldsData'][0]['name'] . '.html">.<b>' . strtoupper($item ['DomainsTldsData'][0]['name']) . "</b> - " . $item ['registration_price'] . " (" . $this->translator->translate('Vat Excluded') . ")</a></li>";
    			}
    		}
    		$html .= "</ul>";
    		$html .= "</li>";
		}
		
		return $html;
	}
}
