<?php
class Shineisp_Validate_Customer extends Zend_Validate_Abstract {

    const ISNOTINDIVIDUAL  = 'If you set the legalform as individual you have to fill also the Taxpayernumber.';
	const ISNOTCOMPANY     = 'If you fill the company field you have to set your legalform as corporation and write down your VAT code.';
	const ISCOMPANY        = 'If you set the legalform as corporation you have to fill also the company name field and VAT.';
	const ISASSOCIATION    = 'If you set the legalform as association you have to fill also the company name field and VAT or Taxpayernumber.';
	
	public function isValid($value, $context = null) {
		
		if ($context ['legalform'] == 1) { // INDIVIDUAL
			// If the customer is a individual it must fill the taxpayernumber
			if( empty( $context['taxpayernumber']) ) {
			    $this->_error( self::ISNOTINDIVIDUAL );
                return false;
			}
			
		} elseif ($context ['legalform'] == 2) { // CORPORATION 
			// If the customer is a company it must fill also the company mandatories fields
		    if ( empty ( $context ['company'] ) || empty ( $context ['vat'] ) ) {
                $this->_error ( self::ISCOMPANY );
                return false;
            }
            

		} elseif ($context ['legalform'] == 3) { // ASSOCIATION 
			// If the customer is a company it must fill also the company mandatories fields
		    if ( empty ( $context ['company'] ) ||  ( empty ( $context ['vat'] ) && empty( $context['taxpayernumber'] ) ) ) {
                $this->_error ( self::ISASSOCIATION);
                return false;
            }
		}
		
		return true;
	}
}
