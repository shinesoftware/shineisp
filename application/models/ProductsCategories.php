<?php

/**
 * ProductsCategories
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class ProductsCategories extends BaseProductsCategories {
	
	/**
	 * grid
	 * create the configuration of the grid
	 */	
	public static function grid($rowNum = 10) {
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'pc.category_id', 'alias' => 'category_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 'pc.category_id', 'alias' => 'category_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Category' ), 'field' => 'pc.name', 'alias' => 'name', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Position' ), 'field' => 'pc.position', 'alias' => 'position', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Enabled' ), 'field' => 'pc.enabled', 'alias' => 'enabled', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['fields'] = "pc.category_id, pc.name as name, pc.enabled as enabled, position as position";
		$config ['datagrid'] ['rownum'] = $rowNum;
		
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'ProductsCategories pc' )->orderBy ( 'pc.name' );
		
		$config ['datagrid'] ['basepath'] = "/admin/productscategories/";
		$config ['datagrid'] ['index'] = "category_id";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
		
		$config ['datagrid'] ['buttons'] ['edit'] ['label'] = $translator->translate ( 'Edit' );
		$config ['datagrid'] ['buttons'] ['edit'] ['cssicon'] = "edit";
		$config ['datagrid'] ['buttons'] ['edit'] ['action'] = "/admin/productscategories/edit/id/%d";
		
		$config ['datagrid'] ['buttons'] ['delete'] ['label'] = $translator->translate ( 'Delete' );
		$config ['datagrid'] ['buttons'] ['delete'] ['cssicon'] = "delete";
		$config ['datagrid'] ['buttons'] ['delete'] ['action'] = "/admin/productscategories/delete/id/%d";
		$config ['datagrid'] ['massactions'] = array ('massdelete'=>'Mass Delete', 'bulkexport'=>'Export' );
		return $config;
	}	
	
	/**
	 * Set a record with a status
	 * 
	 * 
	 * @param $id, $status
	 * @return Void
	 */
	public static function setStatus($id, $status) {
		$item = Doctrine::getTable ( 'ProductsCategories' )->find ( $id );
		$item->status_id = $status;
		return $item->save ();
	}
	
	/**
	 * Set a record with a status
	 * 
	 * 
	 * @param $id, $status
	 * @return Void
	 */
	public static function SaveAll($id, $params) {
	
		if (is_numeric ( $id )) {
			$productscategories = ProductsCategories::getID( $id );
		}else{
			$productscategories = new ProductsCategories();
		}
		
		// Set the URI of the category
		$params ['uri'] = !empty($params ['uri']) ? Shineisp_Commons_UrlRewrites::format($params ['uri']) : Shineisp_Commons_UrlRewrites::format($params ['name']);
		
		$productscategories['name'] = $params ['name'];
		$productscategories['parent'] = $params ['parent'];
		$productscategories['enabled'] = $params ['enabled'] ? 1 : 0;
		$productscategories['description'] = $params ['description'];
		$productscategories['keywords'] = $params ['keywords'];
		$productscategories['uri'] = $params ['uri'];
		$productscategories['googlecategs'] = $params ['googlecategs'];
		$productscategories['position'] = is_numeric($params ['position']) ? $params ['position'] : 0;
		$productscategories['blocks'] = $params ['blocks'];
		
		// Save the data
		$productscategories->save ();
		
		return $productscategories['category_id'];
	}
	
	/**
	 * Get all data 
	 * 
	 * 
	 * @param $id
	 * @return Array
	 */
	public static function getAllInfo($id, $fields = "*") {
		
		try {
			$categories = Doctrine_Query::create ()->select ( $fields )
													->from ( 'ProductsCategories c' )
													->where ( "category_id = ?", $id )
													->limit ( 1 )
													->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );

			// Get all the products attached to this category
			if(!empty($categories[0])){
				$categories[0]['products'] = self::getProductListbyCatID($id, "product_id");
			}
			
			return !empty($categories[0]) ? $categories[0] : array();
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
	
	/**
	 * Add a new Categories
	 * 
	 * @param string $name
	 * @param string $uri
	 * @param integer $parent
	 * @param integer $enabled
	 * @param integer $position
	 * @param string $description
	 * @param string $keywords
	 * @param integer $externalid
	 * @param array $custom
	 * @param string $blocks
	 */
	public static function addNew($name, $uri, $parent=0, $enabled=1, $position=null, $description="", $keywords="", $externalid=null, $custom=null, $blocks=""){
		$cat = new ProductsCategories();
		$cat->name = $name;
		$cat->parent = $parent;
		$cat->enabled = $enabled;
		$cat->description = $description;
		$cat->keywords = $keywords;
		$cat->uri = Shineisp_Commons_UrlRewrites::format($uri);
		$cat->blocks = $blocks;
		$cat->position = $position;
		$cat->externalid = $externalid;
		$cat->custom = $custom;
		if($cat->trySave ()){
			return $cat['category_id'];	
		}
	}
	
	/**
	 * Delete all the categories by custom value
	 */
	public static function clearByCustomValue($value){
		return Doctrine::getTable ( 'ProductsCategories' )->findByCustom($value)->delete();
	}
	
	/**
	 * Delete all the categories
	 */
	public static function clearAll(){
		return Doctrine::getTable ( 'ProductsCategories' )->findAll()->delete();
	}
	
	/**
	 * getAllInfobyURI
	 * Get all data 
	 * @param $uri
	 * @return Doctrine Record / Array
	 */
	public static function getAllInfobyURI($uri, $fields = "*") {
		
		try {
			$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'ProductsCategories c' )->where ( "uri = ?", $uri )->limit ( 1 );
			return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
	/**
	 * getProductListbyCatID
	 * Get a list ready of products using the category ID
	 * @return array
	 */
	public static function getProductListbyCatID($id, $fields = "*", $locale=1) {
		$data = array ();
		
		if ($fields != "*") {
			$fields .= ",p.categories, p.type";
		}
		
		$dq = Doctrine_Query::create ()->select ( $fields )
										->from ( 'Products p' )
										->leftJoin("p.ProductsData pd WITH pd.language_id = $locale")
										->leftJoin("p.ProductsAttributesGroups pag")
										->where('p.enabled = ?', 1)
										->orderBy('position');
		$products = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		foreach ( $products as $product ) {
			
			if (isset ( $product ['categories'] ) && count ( $product ['categories'] ) > 0) {
				$categories = explode ( "/", $product ['categories'] );
				if (in_array ( $id, $categories )) {
					$data [] = $product;
				}
			}
			
		}
		return $data;
	}
	
	/**
	 * Get a list ready of producs using the category Uri
	 * @return array
	 */
	public static function getProductListbyCatUri($uri, $fields = "*", $locale = 1, $rows = 8) {
		$data   = array ();
		$isp_id = Zend_Registry::get('ISP')->isp_id;
		$locale = intval($locale);
		$rows   = intval($rows);

		//$category = Doctrine::getTable ( 'ProductsCategories' )->findBy ( 'uri', $uri, Doctrine_Core::HYDRATE_ARRAY );
		$category = Doctrine_Query::create ()->select ( 'category_id' )->from ( 'ProductsCategories' )
											 ->where('isp_id = ?', $isp_id)
											 ->addWhere('uri = ?', $uri)
											 ->execute(array(), Doctrine_Core::HYDRATE_ARRAY );
											 
		$category    = is_array($category) ? array_shift($category) : null;
		$category_id = isset($category['category_id']) ? intval($category['category_id']) : 0;
		
		if ( $category_id ) {
			$dq = Doctrine_Query::create ()
									->select ( $fields )
									->from ( 'Products p' )
	                                ->leftJoin("p.ProductsData pd WITH pd.language_id = ".$locale)
	                                ->leftJoin("p.ProductsAttributesGroups pag")
	                                ->where('p.enabled = ?', 1)
								    ->addWhere('p.isp_id = ?', $isp_id)
								    ->andWhere("(categories LIKE '".$category_id."/%' OR categories LIKE '%/".$category_id."' OR categories LIKE '%/".$category_id."/%' OR categories = '$category_id')")
									->orderBy('position asc');
			$dq       = self::pager($dq, $rows);		
			$products = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
			
			foreach ( $products as $product ) {
				$categories = explode ( "/", $product ['categories'] );
				if (in_array ( $category_id, $categories )) {
					$data ['records'][] = ProductsData::checkTranslation($product);  // Check the product data translation text
				}
			}
			
			$data['pager'] = $dq->display ( null, true );
		}

		return $data;
	}
	
	/**
	 * Create the array category tree
	 * @param string $uri
	 */
	public static function createCategoryTree($id, $categoriesel = array()) {
		$cats = array ();
		$isfolder = false;
		$items = ProductsCategories::getbyParentId ( $id, 0 );
		foreach ( $items as $category ) {
			$subcategory = self::createCategoryTree ( $category ['category_id'], $categoriesel );
			$isfolder = ($subcategory) ? true : false;
			$selected = in_array ( $category ['category_id'], $categoriesel ) ? true : false;
			
			if ($subcategory) {
				$expanded = in_array ( $category ['category_id'], $categoriesel ) ? true : false;
				$cats [] = array ('key' => $category ['category_id'], 'title' => $category ['name'], 'expand' => $expanded, 'select' => $selected, 'isFolder' => $isfolder, 'children' => $subcategory );
			} else {
				$cats [] = array ('key' => $category ['category_id'], 'title' => $category ['name'], 'select' => $selected );
			}
		}
		return $cats;
	}
	
	
	/**
	 * Count the list of product in a specific category
	 * 
	 * @return integer
	 */
	public static function countProducts($id) {
		$data = array ();
		
		$dq = Doctrine_Query::create ()->select('count(*) as total')->from ( 'Products p' )
		                               ->where('p.enabled = ?', 1)
		                               ->orderBy('position asc');

		// Get the category selected in the products table 
		// where the categories have been written in this way: \12\224\85\53
		$reg1 = "categories like '".$id."/%'";
		$reg2 = "categories like '%/".$id."'";
		$reg3 = "categories like '%/".$id."/%'";
		$reg4 = "categories = '$id'";
		$dq->andWhere ( "($reg1 OR $reg2 OR $reg3 OR $reg4)");
		$rs = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		return $rs[0]['total'];
	}
	
	/**
	 *  Set the paging tool
	 */
	public static function pager($dq, $rows=10){
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		$page = Zend_Controller_Front::getInstance ()->getRequest ()->getParam ( 'page' );
		$category = Zend_Controller_Front::getInstance ()->getRequest ()->getParam ( 'q' );
		$page = ! empty ( $page ) && is_numeric ( $page ) ? $page : 1;

		$pagerLayout = new Doctrine_Pager_Layout ( new Doctrine_Pager ( $dq, $page, $rows ), 
					   new Doctrine_Pager_Range_Sliding ( array ('chunk' => 10 ) ), "/$module/$controller/list/q/$category/page/{%page_number}");
		
		$pagerLayout->setTemplate ( '<a href="{%url}">{%page}</a> ' );
		$pagerLayout->setSelectedTemplate ( '<a class="active" href="{%url}">{%page}</a> ' );
		
		return $pagerLayout;
	}
	
	/**
	 * getGoogleCategories
	 * get all the google categories information by categories ID
	 * $categories = 1/2/5
	 * @params string $categories 
	*/
	public static function getGoogleCategories($categories) {
		$cats = array ();
		$i = 0;
		$categories = explode ( "/", $categories );
		foreach ( $categories as $categoryid ) {
			$category = self::find ( $categoryid, "category_id, name, googlecategs", true );
			if (! empty ( $category [0]['googlecategs'] )) {
				$cats [$i] ['id'] = $category [0] ['category_id'];
				$cats [$i] ['name'] = $category [0] ['name'];
				$cats [$i] ['googlecategs'] = $category [0] ['googlecategs'];
				$i ++;
			}
		}
		return $cats;
	}
		
	/*
	 * getCategoriesInfo
	 * get all the categories information by categories ID
	 * $categories = 1/2/5 
	 */
	public static function getCategoriesInfo($categories) {
		$cats = array ();
		$i = 0;
		$categories = explode ( "/", $categories );
		foreach ( $categories as $categoryid ) {
			$category = self::find ( $categoryid, "category_id, name, uri", true );
			if (! empty ( $category [0] )) {
				$cats [$i] ['id'] = $category [0] ['category_id'];
				$cats [$i] ['name'] = $category [0] ['name'];
				$cats [$i] ['uri'] = $category [0] ['uri'];
				$i ++;
			}
		}
		return $cats;
	}
	
	/**
	 * getList
	 * Get a list ready for the html select object
	 * @return array
	 */
	public static function getList($empty = false) {
		$items = array ();
		$arrTypes = Doctrine::getTable ( 'ProductsCategories' )->findAll ();
	
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		if ($empty) {
			$items [] = $translator->translate ( 'Select ...' );
		}
		
		$items ['domains'] = $translator->translate ( 'Domains' );
		foreach ( $arrTypes->getData () as $c ) {
			$items [$c ['category_id']] = $c ['name'];
		}
		
		return $items;
	}

	/**
	 * getListForSettings
	 * Get a list ready for the selectbox in general settings page
	 * @return array
	 */
	public static function getListForSettings() {
		$items = array ();
		$arrTypes = Doctrine::getTable ( 'ProductsCategories' )->findAll ();
	
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		//$items [] = $translator->translate ( 'Select ...' );
		
		foreach ( $arrTypes->getData () as $c ) {
			$items [$c ['category_id']] = $c ['name'];
		}
		
		return $items;
	}

	
	/**
	 * getAll
	 * Get all categories
	 * @return array
	 */
	public static function getAll() {
		$dq = Doctrine_Query::create ()->select ( 'c.category_id as id, c.name, c.parent' )->from ( 'ProductsCategories c' );
		return $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * Get a category by parent id
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $enabled
	 * @param unknown_type $onlyNotEmpty
	 */
	public static function getbyParentId($id, $enabled=1, $onlyNotEmpty=false) {
		$dq = Doctrine_Query::create ()->select ( 'c.category_id as id, c.name, c.parent, c.uri, c.description, c.keywords' )
										->from ( 'ProductsCategories c' )
										->where ( 'parent = ?', $id )
										->orderBy('c.position, c.name');
		if($enabled){
			$dq->andWhere('enabled = ?', 1);
		}
		
		$records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		$categories = array();
		
		// Get all the categories that contain one product at least
		if($onlyNotEmpty){
			foreach ($records as $category) {
				$total = self::countProducts($category['category_id']);
				if($total){
					$categories[] = $category;
				}
			}
		}else{
			$categories = $records;
		}
		
		return $categories;
	}
	
	/**
	 * Get the complete category menu
	 * 
	 */
	public static function getMenu() {
		$isp_id     = Isp::getCurrentId();
		$categories = array();
		
		$dq = Doctrine_Query::create ()->select ( 'c.category_id as id, c.name, c.parent, c.uri, c.description, c.keywords' )
										->from ( 'ProductsCategories c' )
										->where('c.enabled = ?', 1)
										->andWhere('c.show_in_menu = ?', 1)
										->andWhere('c.isp_id = ?', $isp_id)
										->orderBy('c.parent, c.position, c.name');
		
		$records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		if(!empty($records)){
			foreach ($records as $category) {
				$total = self::countProducts($category['id']);
				if($total){
					$categories[] = $category;
				}
			}
		}
		return $categories;
	}
	
	/**
	 * Get a category by externalid
	 * 
	 * @param $externalid
	 * @return array
	 */
	public static function getbyExtenalId($externalid) {
		return Doctrine_Query::create ()->from ( 'ProductsCategories c' )
										->where ( 'externalid = ?', $externalid )
										->orderBy('c.name')
										->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * Get a record by ID
	 * 
	 * @param $id
	 * @return Doctrine Record
	 */
	public static function find($id, $fields = "*", $retarray = false) {

		if(is_numeric($id)){
			$dq = Doctrine_Query::create ()->select ( $fields )
											->from ( 'ProductsCategories c' )
											->where ( "c.category_id = $id" )
											->limit ( 1 );
			
			$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
			$record = $dq->execute ( array (), $retarray );
			return $record;
		}else{
			return array();
		}
			
	}
	
	/**
	 * getBlocks
	 * Get the category blocks
	 * @param $id
	 * @return Array
	 */
	public static function getBlocks($id) {
		$record = Doctrine_Query::create ()->select ( 'blocks' )->from ( 'ProductsCategories c' )->where ( "c.category_id = ?", $id )->limit ( 1 )->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		return !empty($record[0]) ? $record[0] : array();
	}
	
	
	/**
	 * getCategoryByURI
	 * Get the category by Uri
	 * @param $uri
	 * @return Array
	 */
	public static function getCategoryByURI($uri) {
		$record = Doctrine_Query::create ()->select ( )->from ( 'ProductsCategories c' )->where ( "c.uri = ?", $uri )->limit ( 1 )->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		return !empty($record[0]) ? $record[0] : array();
	}
	
	/**
	 * get_by_id
	 * Get the category by ID
	 * @param $id
	 * @return Array
	 */
	public static function get_by_id($id) {
		$record = Doctrine_Query::create ()->select ( )->from ( 'ProductsCategories c' )->where ( "c.category_id = ?", $id )->limit ( 1 )->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		return !empty($record[0]) ? $record[0] : array();
	}
	
	/**
	 * getID
	 * Enter description here ...
	 * @param unknown_type $id
	 * @return Ambiguous|NULL
	 */
	public static function getID($id) {
		if(is_numeric($id)){
			return Doctrine::getTable('ProductsCategories')->find($id);
		}else{
			return null;
		}
	}
	
	
	/**
	 * massdelete
	 * delete the categories selected 
	 * @param array
	 * @return Boolean
	 */
	public static function massdelete($items) {
		$retval = Doctrine_Query::create ()->delete ()->from ( 'ProductsCategories c' )->whereIn ( 'c.category_id', $items )->execute ();
		return $retval;
	}


	######################################### BULK ACTIONS ############################################
	
	
	/**
	 * massdelete
	 * delete the tickets selected 
	 * @param array
	 * @return Boolean
	 */
	public static function bulk_delete($items) {
		if(!empty($items)){
			return self::massdelete($items);
		}
		return false;
	}
		
}