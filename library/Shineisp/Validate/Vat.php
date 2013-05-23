<?php
class Shineisp_Validate_Vat extends Zend_Validate_Abstract {
	
	const INVALIDVAT        = 'The VAT code is wrong.';
    const ISNOTCOUNTRYID    = 'If you set the legalform as corporation you have to fill also Country.';
    
    private $european_union_countries = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK'
                                            , 'EE', 'EL', 'ES', 'FI', 'FR', 'GB', 'HU'
                                            , 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL'
                                            , 'PL', 'PT', 'RO', 'SE', 'SI', 'SK');
                                            
    private $vies_soap_url = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
    
    private $vat         = '';
    private $countryCode = '';
    
    //* Output resuto from VIES call
    public $viesOutput   = array();
    
    private function eu_check() {
        $VIES = new SoapClient($this->vies_soap_url);

        if ($VIES) {
            try {
                $r = $VIES->checkVat(array('countryCode' => $this->countryCode, 'vatNumber' => $this->vat));

                foreach ($r as $chiave => $valore) {
                    $this->viesOutput[$chiave] = $valore;
                }
                return $r->valid;
  
            } catch(SoapFault $e) {
                $ret   = $e->faultstring;
                $regex = '/\{ \'([A-Z_]*)\' \}/';
                $n     = preg_match($regex, $ret, $matches);
                
                $ret   = !empty($matches[1]) ? $matches[1] : $ret;
                $faults = array (
                    'INVALID_INPUT'       => 'The provided CountryCode is invalid or the VAT number is empty',
                    'SERVICE_UNAVAILABLE' => 'The SOAP service is unavailable, try again later',
                    'MS_UNAVAILABLE'      => 'The Member State service is unavailable, try again later or with another Member State',
                    'TIMEOUT'             => 'The Member State service could not be reached in time, try again later or with another Member State',
                    'SERVER_BUSY'         => 'The service cannot process your request. Try again later.'
                );
                $ret = $faults[$ret];

                // adding a log message
                Shineisp_Commons_Utilities::log("Response from VIES: ".$ret);
                
                $subject    = 'Invalid VAT code';
                $body       = "Response from VIES: ".$ret;            
                $isp = Isp::getActiveISP ();
                Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, $subject, $body );                
                return false;
            }
            
        } else {
        	
            $subject    = 'Connect to VIES';
            $body       = "Impossible to connect with VIES";;            
            $isp = Isp::getActiveISP ();
            Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, $subject, $body );             
            
            // adding a log message
            Shineisp_Commons_Utilities::log("Response from VIES: ".$ret);
            return false;
        }

        return true;
    }    
	
	public function isValid($value , $context = null) {
		if (! empty ( $value )) {
			
            $countryid  = intval($context['country_id']);
            if( $countryid == 0 ) {
                $this->_error( self::ISNOTCOUNTRYID );
                return false;
            }
            
            if( Countries::isITbyId($countryid) ) {
                if ( !is_numeric($value) || strlen($value) != 11 ) {
                    $this->_error( self::INVALIDVAT );
                    return false;
                }
            } elseif ( Countries::isUEbyId( $countryid ) ) {
                return $this->eu_check();
            }
        }
        
        return true;
	}
}
