<?php

class Shineisp_Api_Products extends Shineisp_Api_Abstract_Action  {
    
    /**
     * Get a specific product 
     * 
     * @param integer $productid
     * @throws Shineisp_Api_Exceptions
     * @return multitype:unknown
     */
    public function get( $productid ){
        $this->authenticate();
    	
    	if( empty( $productid ) ) {
            throw new Shineisp_Api_Exceptions( 400002, ":: 'productid' field" );
            exit();
        }
        
        $product    = new Products ();
        $details    = $product->getAllInfo ( $productid );
        
        $details['ProductsUpgrades'] = ProductsUpgrades::getItemsbyProductID($productid);
        return $details;
    }
    
}