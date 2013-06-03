<?php
/**
 * Shineisp_Custom_Productlist
 * This class get all the products in the database
 * @author Shine Software
 *
 */


class Shineisp_Custom_Productlistattributes {
	/*
	 * Show
	 * List of all the products
	 */
	public function Show($parameters){
		$output = "";
		$ns = new Zend_Session_Namespace ();
		$languageID = Languages::get_language_id($ns->lang);
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		if(!empty($parameters['category']) && is_numeric($parameters['category'])){
			$id = $parameters['category'];
		}else{
			return "";
		}
			
		// Get the products
		$products = ProductsCategories::getProductListbyCatID($id, "p.product_id, p.ishighlighted as ishighlighted, p.uri as uri, pd.name as name, pd.nickname as nickname, pd.shortdescription as description", $languageID);
		
		if(!empty($products)){
			$output = "<ul id='pricinggrid' class='pricinggrid'>";
			
			foreach ($products as $product){
				$attributes = ProductsAttributesIndexes::getAttributebyProductID($product['product_id']);
				$price = Products::getPrices($product['product_id']);
				$ribbon = ($product['ishighlighted']) ? "ribbon2" : "";
				
				$output .= "<li>";
				$output .= "<div class='pricingbox'>";
				$output .= "<div class='$ribbon'></div>";
				$output .= '<div class="head" title="' . Shineisp_Commons_Utilities::truncate(strip_tags($product['description']), 150, "...", false, true) . '">
								<div class="title"><a href="/' . $product['uri'] . '.html">' . $product['nickname'] . '</a></div>
							</div>';
			
				if($price['type'] == "multiple"){
					
					$output .= "<div class='price'>" . $translator->translate("Starting at") . "<br>" . $price['minvalue'] . "<span> " . $translator->translate($price['measurement']) . "</span>";
					$output .="<div class='discount'>";
					
					if(!empty($price['discount']) && $price['discount'] > 0){
						$output .= $translator->_("Save %s", $price['discount']);
					}
					$output .="&nbsp;</div></div>";
				}elseif ($price['type'] == "flat"){
					$output .= "<div class='price'>" . $price['value'] . "</div>";
				}	
				
				$output .= '<ul class="attributes" >';
						
				foreach ($attributes as $attribute){
					
					if($attribute['on_product_listing']){
						$output .= "<li><div class='value' title='".$attribute['description']."'>" . $attribute['prefix']. " " . $attribute['value']. " " . $attribute['suffix']. "</div><div class='label'>" . $attribute['label']. "</div></li>";
					}
				}
				$output .=  "</ul><a href='/" . $product['uri'] .".html' class='orange-button'>" . $translator->translate('Buy Now') . "</a></li>";
			}
			
			$output .= "</ul>";
			$output .= "<div class='clear'></div>";
		}
		
		return $output;
	}
}