<?
class Shineisp_Api_Shineisp_Exceptions extends Exception {
    
    private $errorguest = array(
        //ERROR 400 - Bad request
         '400001' => 'There was a problem during the login.'
        ,'400002' => 'Mandary fields is empty'
        ,'400003' => 'Resource not found'
        //ERROR 401 - Not authorized
        ,'401001' => 'User has been not found'
        ,'401002' => 'The email address or password is incorrect.'
        //ERROR 403 - Forbidden
        ,'403001' => 'Username or password empty'
    ); 
    
    public function __construct( $code, $message = "" ) {
        $this->code = $code;
        parent::__construct( $this->errorguest[$code].$message,$code );
    }
    
        
}