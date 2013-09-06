<?
class Shineisp_Api_Provinces extends Shineisp_Api_Abstract_Action  {
    
    public function getAll( $regionid ) {
        $this->authenticate();
        
        if( empty( $regionid ) ) {
            throw new Shineisp_Api_Exceptions( 400002, ":: 'regionid' field" );
            exit();
        } 
        
        $provinces = Provinces::findAllByRegionID($regionid);
        if( empty($provinces) ) {
            return false;
        }        
        
        return $provinces;
    }
    
}