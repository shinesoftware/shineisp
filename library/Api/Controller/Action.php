<?php
abstract class Api_Controller_Action extends Zend_Controller_Action {
    protected $format;
    
     /**
     * preDispatch
     * Starting of the module
     * (non-PHPdoc)
     * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
     */
    public function init(){
        $this->_helper->layout()->disableLayout();
        echo '<pre>';
        print_r($this->_helper->layout());
        die();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->getHelper ( 'layout' )->setLayout ( 'system' );
    }
    
    protected function success( $response ){
        $array  = array (
                         'result'   => 'success'
                        ,'response' => $response
                    );
           
        if( $this->format == 'xml' ) {
            $output = $this->_helper->xmlLoader('createFromArray',array( $array ));
        } else {
            $output = json_encode($array);
        }
        
        return $output;
    }      
    
}
