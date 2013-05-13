<?php

/**
 * Wikilinks
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Wikilinks extends BaseWikilinks
{
	/**
	 * Add a group of wiki pages selected to a product
	 * @param integer $product_id
	 * @param array $wikiIDS
	 * @param integer $locale
	 */
	public static function addWikiPages2Products($product_id, $wikiIDS) {
		$records = new Doctrine_Collection('Wikilinks');
		
		self::removeWikiProductsPages($product_id);
		
		for($i=0;$i<count($wikiIDS);$i++) {
			$records[$i]->product_id = $product_id;
			$records[$i]->wiki_id = $wikiIDS[$i];
		}
		
		$records->save();
		return true;
	}
	
	/**
	 * Add a group of wiki pages selected to a category
	 * @param integer $category_id
	 * @param array $wikiIDS
	 * @param integer $locale
	 */
	public static function addWikiPages2Categories($category_id, $wikiIDS) {
		$records = new Doctrine_Collection('Wikilinks');
		
		self::removeWikiCategoriesPages($category_id);
		
		for($i=0;$i<count($wikiIDS);$i++) {
			$records[$i]->category_id = $category_id;
			$records[$i]->wiki_id = $wikiIDS[$i];
		}
		
		$records->save();
		
		return true;
	}
	
	/**
	 * Delete all the reference between product and wiki pages
	 * @param integer $product_id
	 */
	public static function removeWikiProductsPages($product_id) {
		return Doctrine_Query::create ()->delete ()->from ( 'Wikilinks' )->whereIn ( 'product_id', $product_id )->execute ();
	}
	
	/**
	 * Delete all the reference between category and wiki pages
	 * @param integer $category_id
	 */
	public static function removeWikiCategoriesPages($category_id) {
		return Doctrine_Query::create ()->delete ()->from ( 'Wikilinks' )->whereIn ( 'category_id', $category_id )->execute ();
	}
	
	/**
	 * Get all the wiki pages attached to a selected product
	 * @param integer $id
	 * @param integer $type [products, categories]
	 * @param integer $locale
	 */
	public static function getWikiPages($id, $type="products", $locale=1) {
		$dq = Doctrine_Query::create ()
						->from ( 'Wikilinks pw' )
						->leftJoin ( 'pw.Wiki w' )
						->where ( "w.language_id = ?", $locale )
						->andWhere ( 'w.active = ?', 1 ); // Active
						
		if($type == "products"){
			$dq->leftJoin ( 'pw.Products p' )->addWhere ( 'pw.product_id = ?', $id );
		}elseif ($type == "categories"){
			$dq->leftJoin ( 'pw.ProductsCategories c' )->addWhere ( 'pw.category_id = ?', $id );
		}
						
		return $dq->execute( array (), Doctrine::HYDRATE_ARRAY );
	}
	
	/**
	 * getWikiPagesList
	 * Get all the indexes of the wiki pages attached to a selected product
	 * @param integer $product_id
	 * @param integer $locale
	 */
	public static function getWikiPagesList($id, $type="products", $locale=1) {
		$items = array();
		
		$dq = Doctrine_Query::create ()
						->select('wiki_id')
						->from ( 'Wikilinks pw' )
						->leftJoin ( 'pw.Wiki w' )
						->where ( "w.language_id = ?", $locale )
						->addWhere ( 'w.active = ?', 1 ); // Active
	
		if($type == "products"){
			$dq->leftJoin ( 'pw.Products p' )->addWhere ( 'pw.product_id = ?', $id );
		}elseif ($type == "categories"){
			$dq->leftJoin ( 'pw.ProductsCategories c' )->addWhere ( 'pw.category_id = ?', $id );
		}

		$records = $dq->execute( array (), Doctrine::HYDRATE_ARRAY );
		foreach ($records as $record) {
			$items[] = $record['wiki_id'];
		}
		
		return $items;
	}
	
	/**
	 * getProductsAttached
	 * Get all the products attached to a wiki page
	 * @param integer $wiki_id
	 * @param integer $locale
	 */
	public static function getProductsAttached($wiki_id, $locale=1) {
		
		return Doctrine_Query::create ()
						->from ( 'Wikilinks pw' )
						->leftJoin ( 'pw.Wiki w' )
						->leftJoin ( 'pw.Products p' )
						->leftJoin ( "p.ProductsData pd WITH pd.language_id = $locale")
						->where ( "w.language_id = ?", $locale )
						->addWhere ( 'pw.wiki_id = ?', $wiki_id ) 
						->addWhere ( 'p.enabled = ?', 1 ) // Active
						->execute( array (), Doctrine::HYDRATE_ARRAY );
	
	}
	
	
}