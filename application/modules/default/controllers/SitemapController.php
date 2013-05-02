<?php

class SitemapController extends Zend_Controller_Action {
	
	/**
	 * indexAction
	 * Create the sitemap for google
	 */
	public function indexAction() {
		$pages = CmsPages::getpages('it_IT');

		// add the homepage
		$cats [] = array ("loc" => "http://" . $_SERVER ['HTTP_HOST'], "changefreq" => "weekly" );
		
		if(!empty($pages)){
			foreach ($pages as $page){
				if(!empty($page['var']) && $page['var'] != "homepage"){
					$cats [] = array ("loc" => "http://" . $_SERVER ['HTTP_HOST'] . "/cms/".$page['var'].".html", "changefreq" => "weekly" );
				}
			}
		}
		
		$products = Products::getServicesandHostings('uri');
		if(!empty($products)){
			foreach ($products as $product){
				if(!empty($product['uri'])){
					$cats [] = array ("loc" => "http://" . $_SERVER ['HTTP_HOST'] . "/".$product['uri'].".html", "changefreq" => "weekly" );
				}
			}
		}
		
		$site_map_container = new Shineisp_Commons_GoogleSitemap ();
		for($i = 0; $i < count ( $cats ); $i ++) {
			$value = $cats [$i];
			$site_map_item = new Shineisp_Commons_GoogleSitemap_Item( $value ['loc'], "", $value ['changefreq'], "0.8" );
			$site_map_container->add_item ( $site_map_item );
		}
		
		header ( "Content-type: application/xml; charset=\"" . $site_map_container->charset . "\"", true );
		header ( 'Pragma: no-cache' );
		
		die ($site_map_container->build ());
	}
}