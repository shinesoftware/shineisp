<?
class Shineisp_Api_Regions extends Shineisp_Api_Abstract_Action  {
    
    public function getAll( $countrycode ) {
        $this->authenticate();
        
        if( empty( $countrycode ) ) {
            throw new Shineisp_Api_Exceptions( 400002, ":: 'countrycode' field" );
            exit();
        }    
        
        $states = Regions::fildAllFromCountryCode($countrycode);
        if( empty($states) ) {
            return false;
        }
        
        return $states;
    }
    
}