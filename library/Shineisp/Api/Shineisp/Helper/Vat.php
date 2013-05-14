<?php
class Shineisp_Api_Shineisp_Helper_Taxpayernumber extends Zend_Controller_Action_Helper_Abstract{
    private $european_union_countries = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK'
                                            , 'EE', 'EL', 'ES', 'FI', 'FR', 'GB', 'HU'
                                            , 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL'
                                            , 'PL', 'PT', 'RO', 'SE', 'SI', 'SK');
    
    
    private $vies_soap_url = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
    
    private $vat         = '';
    private $countryCode = '';
    
    //* Array contenente l'output dei dati ritornati dal VIES
    public $viesOutput   = array();
        
    /*
     * Check if VAT Number is Italian
     */
    private function it_check() {
        $sum = 0;
        $odd = strlen($this->vat) % 2;
 
        for($i = 0; $i < strlen($this->vat); $i++) {
            $sum += $odd ? $this->vat[$i] : (($this->vat[$i] * 2 > 9) ? $this->vat[$i] * 2 - 9 : $this->vat[$i] * 2);
            $odd = !$odd;
        }
 
        //Check if is valid
        $isValid = ($sum % 10 == 0) ? true : false;
        if ( $isValid ) {
            $this->eu_check();
        }
        
        return $isValid;
    }
        
        
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
                $ret   = $matches[1];
                $faults = array (
                    'INVALID_INPUT'       => 'The provided CountryCode is invalid or the VAT number is empty',
                    'SERVICE_UNAVAILABLE' => 'The SOAP service is unavailable, try again later',
                    'MS_UNAVAILABLE'      => 'The Member State service is unavailable, try again later or with another Member State',
                    'TIMEOUT'             => 'The Member State service could not be reached in time, try again later or with another Member State',
                    'SERVER_BUSY'         => 'The service cannot process your request. Try again later.'
                );
                $ret = $faults[$ret];
                
                //echo 'Error "'.$ret.'", see message: '.$e->faultstring;
                return false;
            }
        } else {
            //* Connessione SOAP non riuscita, ritorno FALSE
            return false;
        }
    }
        
        
        
        
        /*
         * Controlla una partita IVA
         */
         public function check($vat, $countryCode = 'IT') {
            //* Setto la partita IVA da controllare (magari qui si possono fare dei check extra)
            $this->vat = $vat;
            
            //* Setto il country code
            $this->countryCode = $countryCode;
            
            switch ( TRUE ) {
                
                //* Verifico sintassi P.IVA Italiana
                case (strtoupper($this->countryCode) == 'IT'):
                    if ( !is_numeric($this->vat) || strlen($this->vat) != 11 ) {
                        return false;
                    }
                    
                    return $this->it_check();
                    break;
                    
                //* Verifico tramite P.IVA tramite VIES
                case (in_array(strtoupper($this->countryCode), $this->european_union_countries)):
                    return $this->eu_check();
                    break;
            }

            //* GUEST - ALE - 20121207: se arrivo qui, la p.iva non e' ne italiana ne europea, ritorno sempre true
            return true;
         }
    }
