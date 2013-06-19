<?php

class ErrorController extends Shineisp_Controller_Default
{
	
   public function preDispatch() {
        $this->getHelper ( 'layout' )->setLayout ( '1column' );
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $mex = $this->_getParam('mex');

        // Ajax controll
        if(Shineisp_Commons_Utilities::isAjax()){
        	echo json_encode($errors->exception->getMessage());
        	die();
        }
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            	
            	// 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Controller has been not found';
                break;
                
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Action has been not found';
                break;
                
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $errors->exception->getMessage();
                break;
        }
        
        if(!empty($mex)){
        	$this->view->message .= "<br/><b>$mex</b>";
        }
       
        // Save the error message in the log file errors.log
        $errormessage = $errors->type . ": " . $errors->exception->getMessage();
        Shineisp_Commons_Utilities::log($errormessage);
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }


}

