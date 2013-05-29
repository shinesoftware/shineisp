<?php
class Shineisp_Validate_Regions extends Zend_Validate_Abstract {
    const COUNTRYIDEMPTY    = 'If you set RegionID you have to fill also CountryID';
    const REGIONIDNOTVALID  = 'RegionID not valid';

    public function isValid($value, $context = null) {
        $value  = intval($value);
        if( $value != 0 ) {
            $countryid  = intval($context['country_id']);
            if( $countryid == 0 ) {
                $this->_error( self::COUNTRYIDEMPTY );
                return false;
            }
            
            $region = Regions::find($value);
            if( $region->country_id != $countryid ) {
                $this->_error( self::REGIONIDNOTVALID );
                return false;
            }
        }
        
        return true;
    }
}
