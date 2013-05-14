<?
class Shineisp_Api_Shineisp_Customers extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function insert( $params ) {
        $this->authenticate();
        
        //Check the formalLegal for different validation
        if( ! array_key_exists('legalform',$params) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400002, ":: 'legalform' field" );
            exit();
        }
        
        $legalform  = intval(trim($params['legalform']));
        if( $legalform == 0 ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400002, ":: 'legalform' field" );
            exit();
        }
        
        $form = new Api_Form_CustomerForm ( array ('action' => '#', 'method' => 'post' ) );
        
        switch( $legalform ) {
            case 1:
                $form->getElement('taxpayernumber')
                     ->addValidator(new Zend_Validate_NotEmpty())
                     ->setAllowEmpty(false);
                
                break;
                
            case 2:
                $form->getElement('vat')
                     ->addValidator(new Zend_Validate_NotEmpty())
                     ->setAllowEmpty(false);
                break;
        }
        
        if ($form->isValid ( $params ) ) {
            return "Valido";
        } else {
            return $form->getMessages();
        }
        return $params;
    }
    
}