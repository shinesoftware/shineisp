<?
class Shineisp_Api_Productscategories extends Shineisp_Api_Abstract_Action  {
    
    public function getall(){
        $this->authenticate();
        
        return ProductsCategories::getMenu();
    }
    
    public function getproducts( $uri ){
        $this->authenticate();
        
        if( empty($uri) ) {
            throw new Shineisp_Api_Exceptions( 400002, ":: 'uri' field" );
            exit();
        }
        
        $infoCategory   = ProductsCategories::getAllInfobyURI( $uri );
        if( empty($infoCategory) ) {
            throw new Shineisp_Api_Exceptions( 400003, ":: uri=>'{$uri}' not category assigned" );
            exit();
        }
        
        //get the first elemnt
        $infoCategory   = array_shift($infoCategory);
        $categoryid     = $infoCategory['category_id'];
        $products       = ProductsCategories::getProductListbyCatID($categoryid);
        
        return $products;
    }
    
}
