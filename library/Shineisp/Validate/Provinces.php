<?php
class Shineisp_Validate_Provinces extends Zend_Validate_Abstract {
    const COUNTRYIDEMPTY    = 'If you set Area you have to fill also CountryID';
    const REGIONIDEMPTY     = 'If you set Area you have to fill also RegionID';
    const PROVINCEIDNOTVALID    = 'If you set Area you have to fill also RegionID';    

    public function isValid($value, $context = null) {
        $value  = intval($value);
        if( $value != 0 ) {
            $countryid  = intval($context['country_id']);
            if( $countryid == 0 ) {
                $this->_error( self::COUNTRYIDEMPTY );
                return false;
            }
            $region_id  = intval($context['region_id']);
            if( $countryid == 0 ) {
                $this->_error( self::REGIONIDEMPTY );
                return false;
            }            
            
            $province = Provinces::find($value);
            if( $province->country_id != $countryid ) {
                $this->_error( self::PROVINCEIDNOTVALID );
                return false;
            }
            
            if( $province->region_id != $region_id ) {
                $this->_error( self::PROVINCEIDNOTVALID );
                return false;
            }
            
        }
        
        return true;
    }
}
