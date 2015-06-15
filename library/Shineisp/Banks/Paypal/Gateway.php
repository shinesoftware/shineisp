<?php

/*
 * Shineisp_Banks_Paypal_Gateway
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Banks_Paypal_Gateway
* Purpose:  Manage the communications with the Gateway
* -------------------------------------------------------------
*/

class Shineisp_Banks_Paypal_Gateway extends Shineisp_Banks_Abstract implements Shineisp_Banks_Interface {

    public function __construct($orderid)
    {
        parent::__construct($orderid);
        parent::setModule(__CLASS__);
    }

    /**
     * CreateForm
     * Create the bank form
     * @return string
     */
    public function CreateForm()
    {
        $order = self::getOrder();
        $bank = self::getModule();
        $translator = self::getTranslator();

        if (empty ($bank ['account'])) {
            return null;
        }

        if ($order) {
            $form = "";
            $url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];
            $item_name = $translator->translate("Order No.") . " " . self::getOrderID() . " - " . date('Y');
            $custom = self::getOrderID();

            if (!self::isHidden()) {
                $form .= "<div class=\"bank_" . $bank ['name'] . "\">" . $bank ['description'] . "</div>";
            }

            $form .= '<form name="_xclick" id="paypal" action="' . $url . '" method="post">';
            $form .= '<input type="hidden" name="cmd" value="_xclick">';

            $form .= '<input type="hidden" name="add" value="1" />';
            $form .= '<input type="hidden" name="business" value="' . $bank ['account'] . '" />';
            $form .= '<input type="hidden" name="item_name" value="' . $item_name . '" />';
            $form .= '<input type="hidden" name="item_number" value="1" />';
            $form .= '<input type="hidden" name="custom" value="' . $custom . '" />';
            $form .= '<input type="hidden" name="amount" value="' . number_format($order ['grandtotal'], 2, '.', '') . '" />';
            $form .= '<input type="hidden" name="currency_code" value="EUR" />';
            $form .= '<input type="hidden" name="rm" value="2">';
            $form .= '<input type="hidden" name="lc" value="IT">';
            $form .= '<input type="hidden" name="notify_url" value="http://' . self::getUrlCallback() . '">';
            $form .= '<input type="hidden" name="return" value="http://' . self::getUrlOk() . '" />';
            $form .= '<input type="hidden" name="cancel_return" value="http://' . self::getUrlKo() . '" />';
            if (!self::doRedirect()) {
                $form .= '<input type="submit" class="button small" name="submit" value="' . $translator->translate('Pay Now') . '">';
            }
            $form .= '</form>';

            if (self::doRedirect()) {

                $form .= $translator->translate('You will be redirected to the secure bank website, please be patient.');
                $form .= "<script type=\"text/javascript\">\nsetTimeout(function () {\n$('form[name=\"_xclick\"]').submit();\n}, 3000);\n</script>";
            }

            return array('name' => $bank ['name'], 'description' => $bank ['description'], 'html' => $form);
        }
    }

    /**
     * Response
     * Create the Order, Invoice and send an email to the customer
     * @param $response from the Gateway Server
     * @return order_id or false
     */
    public function Response($response)
    {
        $bank = self::getModule();
        $bankid = $bank ['bank_id'];

        Shineisp_Commons_Utilities::logs("Paypal Response " . json_encode($response), "paypal.log");

        if (!empty ($response ['item_number'])) {

            // Get the indexes of the order
            $orderid = trim($response ['custom']);

            if (is_numeric($orderid) && is_numeric($bankid)) {

                // Replacing the comma with the dot in the amount value.
                $amount = str_replace(",", ".", $response ['amount']);

                $payment = Payments::addpayment($orderid, $response ['thx_id'], $bankid, 0, $amount);
                Orders::set_status($orderid, Statuses::id("paid", "orders")); // Paid
                OrdersItems::set_statuses($orderid, Statuses::id("paid", "orders")); // Paid


                return $orderid;
            }
        }

    }

    /**
     * CallBack
     * This function is called by the bank server in order to confirm the transaction previously executed
     * @param $response from the Gateway Server
     * @return boolean
     */
    public function CallBack($response)
    {
        $bank = self::getModule();
        $url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];

        // Resend all the variables to paypal to confirm the receipt message
        $result = self::processIPN($response);

        if (!empty ($response ['custom'])) {

            // Get the orderid back from the bank post variables
            $orderid = trim($response ['custom']);

            if (!empty ($orderid) && is_numeric($orderid)) {
                //check the ipn result received back from paypal
                if ($result) {
                    Orders::Complete($orderid, true); // Complete the order information and it executes all the tasks to do
                    Payments::confirm($orderid, true); // Set the payment confirm
                } else {
                    Payments::confirm($orderid, false);
                }
            }
        }
        die;
    }

    /**
     * processIPN
     * @return boolean
     */
    private function processIPN($response)
    {
        $bank = self::getModule();
        $url = $bank ['test_mode'] ? $bank ['url_test'] : $bank ['url_official'];
        Shineisp_Commons_Utilities::logs("Paypal IPN Process [$url]", "paypal.log");

        unset($response['controller']);
        unset($response['action']);
        unset($response['module']);
        unset($response['gateway']);

        Shineisp_Commons_Utilities::logs(json_encode($response), "paypal.log");

        // Set up request to PayPal
        $request = curl_init();
        curl_setopt_array($request, array
        (
            CURLOPT_URL => $url,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($response + array('cmd' => '_notify-validate')),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HEADER => FALSE,
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_CAINFO => 'cacert.pem',
        ));

        // Execute request and get response and status code
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);

        // Close connection
        curl_close($request);
        Shineisp_Commons_Utilities::logs("Response: $response Status: $status", "paypal.log");

        if ($status == 200 && $response == 'VERIFIED') {
            return true;
        } else {
            return false;
        }
    }
}