<?php

/*
 * Shineisp_Banks_BNL_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_BNL_Gateway
* Purpose:  Manage the communications with the BNL IGFS mode
* -------------------------------------------------------------
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

            $html = "<div class=\"bank_" . $bank ['name'] . "\">" . $bank ['description'] . "</div>";

            $url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];
            $item_name = $translator->translate ( "Order No." ) . " " . $order['order_number'];
            list($tid, $ksig) = explode(":", $bank ['account']);

            $init = new Shineisp_Banks_BNL_Igfs_CgInit();
            $init->disableCheckSSLCert();
            $init->serverURL = $url;
            $init->errorURL = "http://" . self::getUrlKo ();
            $init->notifyURL = "http://" . self::getUrlOk() . "/custom/" . self::getOrderID();
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

            Shineisp_Commons_Utilities::logs("-----> Request: " . json_encode((array)$init), "bnl_igfs.log");
            $result = $init->execute();

            if($result){

                $paymentId = $init->paymentID;
                $session = new Zend_Session_Namespace ( 'Default' );
                $session->paymentid = $paymentId;

                Shineisp_Commons_Utilities::logs ( "-----> The bank replies with a new paymentID: $paymentId", "bnl_igfs.log" );
                Shineisp_Commons_Utilities::logs("-----> The user has been red replies with a new paymentID: $paymentId", "bnl_igfs.log");
                $html .= "<a class='btn btn-success' href='" . $init->redirectURL . "'>" . $translator->translate('Pay Now') . "</a>";

            }else{
                Shineisp_Commons_Utilities::logs ( $init->errorDesc, "bnl_igfs.log" );
                $html .= $init->errorDesc;

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
        Shineisp_Commons_Utilities::logs("---> IgfsCgVerify Request: $requestdata", 'bnl_igfs.log');

        $result = $IgfsCgVerify->execute();

        $responsedata = json_encode($IgfsCgVerify, true);
        Shineisp_Commons_Utilities::logs("-----> IgfsCgVerify Response: $responsedata", 'bnl_igfs.log');

        if($IgfsCgVerify->error){
            Shineisp_Commons_Utilities::logs("-----> " . $IgfsCgVerify->rc . ": " . $IgfsCgVerify->error, 'bnl_igfs.log');
            return false;
        }

        #Zend_Debug::dump($IgfsCgVerify);

        // Get the orderid back from the bank post variables
        $orderid = trim ( $response ['custom'] );
        $order = self::getOrder();
        $amount = $order['grandtotal'];

        Shineisp_Commons_Utilities::logs("Adding the payment information: " . $IgfsCgVerify->tranID, "bnl_igfs.log");
        $payment = Payments::addpayment($orderid, $IgfsCgVerify->tranID, $bankid, 0, $amount, date('Y-m-d H:i:s'), $order['customer_id'], $IgfsCgVerify->errorDesc);

        Shineisp_Commons_Utilities::logs("Set the order in the processing mode", "bnl_igfs.log");
        Orders::set_status($orderid, Statuses::id("paid", "orders")); // Paid
        OrdersItems::set_status($orderid, Statuses::id("paid", "orders")); // Paid

        Shineisp_Commons_Utilities::logs("End callback", "bnl_igfs.log");

        return $orderid;
    }


    /**
     * CallBack
     * This function is called by the bank server in order to confirm the transaction previously executed
     * @param $response from the Gateway Server
     * @return boolean
     */
    public function CallBack($response)
    {

    }
}