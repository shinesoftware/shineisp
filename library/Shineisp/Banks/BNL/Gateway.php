<?php

/*
 * Shineisp_Banks_BNL_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_BNL_Gateway
* Purpose:  Manage the communications with the IWBANK
* -------------------------------------------------------------
* 
* TEST DATA:
* 
*   username: IWSTEST2
*   password: 12345
*   second password: MONETA
*  
* 
*/

class Shineisp_Banks_BNL_Gateway extends Shineisp_Banks_Abstract implements Shineisp_Banks_Interface {

    public function __construct($orderid) {
        parent::__construct ( $orderid );
        parent::setModule ( __CLASS__ );
    }

    /**
     * CreateForm
     * Create the bank form
     * @return string
     */
    public function CreateForm() {
        $order      = self::getOrder ();
        $bank       = self::getModule ();
        $translator = self::getTranslator ();
        $html       = null;

        // Check the bank account field value
        if (empty ( $bank ['account'] )) {
            return null;
        }

        if ($order) {

            $url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];
            $item_name = $translator->translate ( "Order No." ) . " " . $order['order_number'];
            list($tid, $ksig) = explode(":", $bank ['account']);

            $init = new Shineisp_Banks_BNL_Igfs_CgInit();
            $init->disableCheckSSLCert();
            $init->serverURL = $url;
            $init->errorURL = "http://" . self::getUrlKo ();
            $init->notifyURL = "http://" . self::getUrlCallback () . "/custom/" . self::getOrderID ();
            $init->timeout = 150000;
            $init->tid = $tid;
            $init->kSig = $ksig;
            $init->shopID = self::getOrderID ();
            $init->shopUserRef = $order ['Customers'] ['email'];
            $init->trType = "PURCHASE";
            $init->currencyCode = "978";
            $init->langID = "IT";
            $init->amount = (float)$order ['grandtotal'] * 100;
            $init->description = $item_name;
            $init->addInfo1 = self::getOrderID ();

            $result = $init->execute();

            if($result){

                $paymentId = $init->paymentID;
                $session = new Zend_Session_Namespace ( 'Default' );
                $session->paymentid = $paymentId;

                Shineisp_Commons_Utilities::logs ( "-----> The bank replies with a new paymentID: $paymentId", "bnl_igfs.log" );

                header("location:" . $init->redirectURL);
                die;

            }else{
                Shineisp_Commons_Utilities::logs ( $init->errorDesc, "bnl_igfs.log" );

                $html = '<html><body>';
                $html .= '<h2>' . $this->__ ( 'Payment module error' ) . '</h2>';
                $html .= '<p>' . $this->__('Payment module has not been set accordingly. Please check the log error file.') . '</p>';
                $html .= '<p>' . $this->__('You have to enable the Log feature in the Magento Administration and execute again the process, a log file will be created.') .'</p>';
                $html .= '</body></html>';
                die($html);
            }

            return array('name' => $bank ['name'], 'description' => $bank ['description'], 'html' => $html);
        }

        return $translator->translate('There was a problem during the %s payment form creation', "BNL");

    }

    /**
     * Response
     * Create the Order, Invoice and send an email to the customer
     * @param $response from the Gateway Server
     * @return order_id or false
     */
    public function Response($response) {

    }


    /**
     * CallBack
     * This function is called by the bank server in order to confirm the transaction previously executed
     * @param $response from the Gateway Server
     * @return boolean
     */
    public function CallBack($response) {

        $bank = self::getModule ();
        $bankid = $bank ['bank_id'];
        $url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];

        list($tid, $ksig) = explode(":", $bank ['account']);

        Shineisp_Commons_Utilities::logs ( "-----> Callback starts!", "bnl_igfs.log" );

        $session = new Zend_Session_Namespace ( 'Default' );

        $IgfsCgVerify 	= new Shineisp_Banks_BNL_Igfs_CgVerify();
        $IgfsCgVerify->disableCheckSSLCert();
        $IgfsCgVerify->timeout = 150000;
        $IgfsCgVerify->paymentID = $session->paymentid;
        $IgfsCgVerify->kSig = $ksig;
        $IgfsCgVerify->shopID = self::getOrderID ();
        $IgfsCgVerify->tid = $tid;
        $IgfsCgVerify->serverURL = $url;

        $requestdata = json_encode($IgfsCgVerify, true);
        Shineisp_Commons_Utilities::logs("-----> IgfsCgVerify Request: $requestdata", 'bnl_igfs.log');

        $result = $IgfsCgVerify->execute();

        $responsedata = json_encode($IgfsCgVerify, true);
        Shineisp_Commons_Utilities::logs("-----> IgfsCgVerify Response: $responsedata", 'bnl_igfs.log');

        Zend_Debug::dump($IgfsCgVerify);

        if($IgfsCgVerify->error){
            Shineisp_Commons_Utilities::logs("-----> " . $IgfsCgVerify->rc . ": " . $IgfsCgVerify->error, 'bnl_igfs.log');
            return false;
        }

        #return self::getOrderID ();
        Zend_Debug::dump($IgfsCgVerify);
        die;
        // Get the orderid back from the bank post variables
        $orderid = trim ( $response ['custom'] );

        return true;
    }
}