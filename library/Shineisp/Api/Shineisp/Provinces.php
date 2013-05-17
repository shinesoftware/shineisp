<?
class Shineisp_Api_Shineisp_Provinces extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function getAll( $regionid ) {
        $this->authenticate();
        
        if( empty( $regionid ) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400002, ":: 'regionid' field" );
            exit();
        } 
        
        $provinces = Provinces::fildAllByRegionID($regionid);
        if( empty($provinces) ) {
            return false;
        }        
        
        return $provinces;
    }
    
}