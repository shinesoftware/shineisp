<?
class Shineisp_Api_Shineisp_Provinces extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function getAll( $stateid ) {
        $this->authenticate();
        
        if( empty( $stateid ) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400002, ":: 'stateid' field" );
            exit();
        } 
        
        $provinces = Provinces::fildAllByStateID($stateid);
        if( empty($provinces) ) {
            return false;
        }        
        
        return $provinces;
    }
    
}