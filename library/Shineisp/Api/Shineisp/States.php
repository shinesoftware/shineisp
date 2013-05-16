<?
class Shineisp_Api_Shineisp_States extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function getAll( $countrycode ) {
        $this->authenticate();
        
        if( empty( $countrycode ) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400002, ":: 'countrycode' field" );
            exit();
        }    
        
        $states = States::fildAllFromCountryCode($countrycode);
        if( empty($states) ) {
            return false;
        }
        
        return $states;
    }
    
}