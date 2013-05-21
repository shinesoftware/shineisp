<?
class Shineisp_Api_Shineisp_Orders extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function create( $params ) {
        $this->authenticate();

        $uuid       = $params['uuid'];
        $customers  = Customers::findWithUuid($uuid);
        if( empty($customers) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'uuid' not valid" );
            exit();
        }
        
        $trancheid  = intval($params['trancheid']);
        $tranche    = ProductsTranches::getTranchebyId($trancheid);
        if( empty($tranche) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'trancheid' not valid" );
            exit();
        }
        
        #Check Products
        if( empty( $params['products']) && ! is_array($params['products']) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: not 'products' choose" );
            exit();            
        }
        
        foreach( $params['products'] as $product ) {
            $productid  = intval( $product['productid']);
            $billingid  = intval( $product['billingid']);
            $ttry       = ProductsTranches::getTranchesBy_ProductId_BillingId($productid, $billingid);
            if( empty( $ttry) ) {
                throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'productid' or 'bilingid' not valid" );
                exit();            
            }
            
            $ttry   = array_shift($ttry);
            if( $ttry['tranche_id'] != $trancheid ) {
                throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'bilingid' not valid" );
                exit();            
            }
        }
        
        
        $id         = $customers['customer_id'];
        
        if( $params['status'] == "complete" ) {
            $status = Statuses::id('complete', 'orders');   
        } else {
            $status = Statuses::id('tobepaid', 'orders');
        }

        $theOrder = Orders::create ( $customers['customer_id'], $status, $params ['note'] );
        
        foreach( $params['products'] as $product ) {
            $productid  = intval( $product['productid']);
            $billingid  = intval( $product['billingid']);
            $quantity   = intval( $product['quantity']);
            $p          = Products::getAllInfo($productid);
            
            $options    = array( 'callback_url' => $product['urlactive'], 'uuid' => $product['uuid']);
            
            $upgrade    = false;
            if( array_key_exists('upgrade', $product) && $product['upgrade'] != false ) {
                $orderItemsUpgrade  = OrdersItems::findByUUID( $product['upgrade'] );
                $fromUpgrade        = $orderItemsUpgrade->toArray();
                $upgrade            = $fromUpgrade['detail_id']; 
            }
            
            Orders::addItem ( $productid, $quantity, $billingid, $trancheid, $p['ProductsData'][0]['name'], $options,$upgrade );
        }

        $orderID = $theOrder ['order_id'];
        if( $params['sendemail'] == 1 ) {
            Orders::sendOrder ( $orderID );
        }     
        
        $banks = Banks::find ( $params['payment'], "*", true );
        if (! empty ( $banks [0] ['classname'] )) {
            
            $class = $banks [0] ['classname'];
            if (class_exists ( $class )) {
                // Get the payment form object
                $banks = Banks::findbyClassname ( $class );
                $gateway = new $class ( $orderID );
                $gateway->setFormHidden ( true );
                $gateway->setRedirect ( true );
                
                $gateway->setUrlOk ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $banks ['classname'] ) );
                $gateway->setUrlKo ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $banks ['classname'] ) );
                $gateway->setUrlCallback ( $_SERVER ['HTTP_HOST'] . "/common/callback/gateway/" . md5 ( $banks ['classname'] ) );
                
                return $gateway->CreateForm ();
            }
        } 
            
        throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: bad request" );
        exit();            
    }

    public function getAll( $uuid ) {
        $customers  = Customers::findWithUuid($uuid);
        if( empty($customers) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'uuid' not valid" );
            exit();
        }
        $id         = $customers['customer_id'];
        
        $fields     = " o.order_id,o.grandtotal as grandtotal,s.status as status,
                        DATE_FORMAT(o.order_date, '%d/%m/%Y') as orderdate, 
                        DATE_FORMAT(o.expiring_date, '%d/%m/%Y') as expiringdate,
                        o.is_renewal as is_renewal, i.number as invoice,
                        c.firstname,c.lastname,c.company";
        return Orders::getOrdersByCustomerID($id,$fields);
    }
    
    public function get( $uuid, $order_uuid = null, $service_uuid = null ) {
        $customers  = Customers::findWithUuid($uuid);
        if( empty($customers) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'uuid' not valid" );
            exit();
        }
        $id         = $customers['customer_id'];
        
        if( $order_uuid == null && $service_uuid == null ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'order_uuid' not valid and 'service_uuid' not valid" );
        }
        
        #TODO get order from $order_uuid
        if( $service_uuid != null ) {
            $objService     = OrdersItems::findByUUID($service_uuid);
            $service        = $objService->toArray();
            $orderid        = $service['order_id'];
            
            $formattedID = Orders::formatOrderId($orderid);
            
            $fields = "o.order_id, 
                        DATE_FORMAT(o.order_date, '%d/%m/%Y') as Starting, 
                        DATE_FORMAT(o.expiring_date, '%d/%m/%Y') as Valid_Up, 
                        in.invoice_id as invoice_id, 
                        in.number as Invoice, 
                        CONCAT(d.domain, '.', w.tld) as Domain, 
                        c.company as company, 
                        o.status_id, 
                        s.status as Status, 
                        o.vat as VAT, 
                        o.total as Total, 
                        o.grandtotal as Grandtotal";
            
            $rs = Orders::getAllInfo ( $orderid, $fields, true, $id );            
            if ( empty ( $rs )) {
                throw new Shineisp_Api_Shineisp_Exceptions( 404001, ":: Orders not found" );
            }

            $currency = Zend_Registry::getInstance ()->Zend_Currency;
            
            $result     = array();
            $order      = array_shift($rs);
            
            // Check the status of the order. 
            // If the order has to be paid we have update it to the last prices and taxes
            if($order['status_id'] == Statuses::id('tobepaid', 'orders')){
                
                // Update the total order
                Orders::updateTotalsOrder($orderid);

                // Reload the data
                $rs = Orders::getAllInfo ( $orderid, $fields, true, $id );
                $order   = array_shift($rs);
                $order['Total']         = $currency->toCurrency($order['Total'], array('currency' => Settings::findbyParam('currency')));
                $order['VAT']           = $currency->toCurrency($order['VAT'], array('currency' => Settings::findbyParam('currency')));
                $order['Grandtotal']    = $currency->toCurrency($order['Grandtotal'], array('currency' => Settings::findbyParam('currency')));
                
                $result['tobepaid'] = true;
            }
            $result['order']    = $order;
            
            $records = OrdersItems::getAllDetails ( $orderid, "oi.detail_id, oi.description as description, DATE_FORMAT(oi.date_end, '%d/%m/%Y') as expiration_date, oi.quantity as quantity, oi.price as price, bc.name as billingcycle, oi.setupfee as setupfee", true );
            for ($i=0; $i<count($records); $i++){
                $records[$i]['price']       = $currency->toCurrency($records[$i]['price'], array('currency' => Settings::findbyParam('currency')));;
                $records[$i]['setupfee']    = $currency->toCurrency($records[$i]['setupfee'], array('currency' => Settings::findbyParam('currency')));;
            }
            
            $result['order-items']  = $records;
            
            $result['invoid-id']        = ($order['status_id'] == Statuses::id("complete", "orders") && $order['Invoice'] > 0) ? true : false;
            $result['invoid-number']    = $order['Invoice'];
            $result['invoid-id']        = $order['invoice_id'];
            
            $result['payments']     = "";
            if( $result['tobepaid'] == true ) {
                $result['payments']  = array ();
                $banks = Banks::findAllActive ( "classname", true );
                
                if (! empty ( $banks )) {
                    foreach ( $banks as $bank ) {
                        if (! empty ( $bank ['classname'] ) && class_exists ( $bank ['classname'] )) {
                            if (class_exists ( $bank ['classname'] )) {
                                
                                $class = $bank ['classname'];
                                $payment = new $class ( $id );
                                $payment->setUrlOk ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $bank ['classname'] ) );
                                $payment->setUrlKo ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $bank ['classname'] )  );
                                $payment->setUrlCallback ( $_SERVER ['HTTP_HOST'] . "/common/callback/gateway/" . md5 ( $bank ['classname'] )  );
                                
                                $result['payments'][]  = $payment->CreateForm ();
                            }
                        }
                    }
                }                 
            }
            
                        
            return $result;
        }
                
    }

}