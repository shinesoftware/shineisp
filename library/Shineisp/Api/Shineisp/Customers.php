<?
class Shineisp_Api_Shineisp_Customers extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function insert( $params ) {
        $this->authenticate();
        
        $form = new Api_Form_CustomerForm ( array ('action' => '#', 'method' => 'post' ) );
        
        if ($form->isValid ( $params ) ) {
            $params['status_id']    = 11;
            $result = Customers::saveAll($params, null);
            if( $result == false ) {
                throw new Shineisp_Api_Shineisp_Exceptions( 400005 );
                exit();
            }
            
            return $result;
            
        } else {
            $errors     = $form->getMessages();
            $message    = "";
            foreach( $errors as  $field => $errorsField ) {
                $message .= "Field '{$field}'<br/>";
                foreach( $errorsField as $error => $describe ) {
                    $message .=" => {$error} ({$describe})";
                }
            }
            
            throw new Shineisp_Api_Shineisp_Exceptions( 400004, ":\n{$message}" );
            exit();
        }
    }
    
}