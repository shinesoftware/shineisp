<?
class Shineisp_Api_Exceptions extends SoapFault {
    
    private $errors = array(
        //ERROR 400 - Bad request
         '400001' => 'There was a problem during the login.'
        ,'400002' => 'Mandary fields are empty'
        ,'400003' => 'Resource not found'
        ,'400004' => 'Request fields are incorrect'
        ,'400005' => 'Error for insert new customers'
        ,'400006' => 'Error for insert new orders'
        ,'400007' => 'Error when print invoice'
        ,'400008' => 'Class not found'
        ,'400009' => 'Class name is empty'
        //ERROR 401 - Not authorized
        ,'401001' => 'User has been not found'
        ,'401002' => 'The email address or password is incorrect.'
        //ERROR 403 - Forbidden
        ,'403001' => 'Username or password empty'
        //ERROR 404
        ,'404001' => 'Resourse not found'
   		//ERROR 405
        ,'405001' => 'OpenId login failed'
    ); 
    
    public function __construct( $code, $message = "" ) {
        parent::__construct( "".$code, $this->errors[$code].$message );
    }
    
        
}