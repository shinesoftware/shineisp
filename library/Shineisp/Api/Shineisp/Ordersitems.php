<?
class Shineisp_Api_Shineisp_Ordersitems extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function checkIfComplete( $uuid ) {
        $this->authenticate();
        
        return OrdersItems::checkIfCompletedByUUID($uuid);
    }
    
    public function getAll( $uuid ) {
        $customers  = Customers::findWithUuid($uuid);
        if( empty($customers) ) {
            throw new Shineisp_Api_Shineisp_Exceptions( 400006, ":: 'uuid' not valid" );
            exit();
        }
        $id         = $customers['customer_id'];
        $services   = Products::getAllServicesByCustomerID ($id, 'o.order_id, oi.detail_id as detail_id, pd.name as productname, 
        DATE_FORMAT(oi.date_start, "%d/%m/%Y") AS date_start, DATE_FORMAT(oi.date_end, "%d/%m/%Y") AS date_end, 
        DATEDIFF(oi.date_end, CURRENT_DATE) AS daysleft, oi.price as price, oi.autorenew as autorenew, oi.uuid as uuid, s.status as status' );        
        
        return $services;
    }    
    
}
    