<?
class Shineisp_Api_Shineisp_Customers extends Shineisp_Api_Shineisp_Abstract_Action  {
    
    public function insert( $params ) {
        $this->authenticate();
        
        $form = new Api_Form_CustomerForm ( array ('action' => '#', 'method' => 'post' ) );
        #Add email validator
        $mailValidator = new Shineisp_Validate_Email();
        $form->getElement('email')->addValidator($mailValidator);
        
        $form->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'decorators' => array('Composite'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'description'      => 'Write here your password. (min.6 chars - max.20 chars)',
            'label'      => 'Password',
            'class'      => 'text-input large-input'
        ));        
        
        
        if( array_key_exists('countrycode', $params) ) {
            $country_id     = Countries::getIDbyCode($params['countrycode']);
            if( $country_id == null ) {
                throw new Shineisp_Api_Shineisp_Exceptions( 400005, ":: 'countrycode' not valid" );
                exit();
            }
            
            unset($params['coutrycode']);
            $params['country_id']   = $country_id;
        }
        
        if( array_key_exists('provincecode',$params) && $params['provincecode'] != "" ) {
            $params['area'] = $params['provincecode'];
            unset($params['provincecode']);
        }
        
        if ($form->isValid ( $params ) ) {
            if( $params['status'] == false ) {
                $params['status'] = 'disabled';    
            }
            
            $idcustomers    = Customers::Create($params);
            
            $customer       = Customers::find($idcustomers);
            return $customer['uuid'];
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

    public function get( $uuid ) {
        $this->authenticate();
        
        $customer   = Customers::findWithUuid($uuid);
        if( empty($customer) ) {
            return false;
        }
        $customerid = $customer['customer_id'];
        $fields = ' c.company as company, c.firstname as firstname, c.lastname as lastname,c.sex,c.email, c.taxpayernumber, c.vat, l.legalform_id as legalformid,c.birthdate as birthdate, 
                    c.sex as sex, a.address_id, a.address as address, a.city as city,a.code as code,a.country_id as countryid ,a.region_id as regionid,a.area as area, ct.code as countrycode
                    ,cn.*,cnts.*';
        $customerid = $customer['customer_id'];
        $customer   = Customers::getAllInfo( $customerid, $fields );
        unset($customer['password']);
        return $customer;

    }
    
    public function update( $uuid, $params ) {
        $this->authenticate();
        
        $form = new Api_Form_CustomerForm ( array ('action' => '#', 'method' => 'post' ) );
        
        if( array_key_exists('countrycode', $params) ) {
            $country_id     = Countries::getIDbyCode($params['countrycode']);
            if( $country_id == null ) {
                throw new Shineisp_Api_Shineisp_Exceptions( 400005, ":: 'countrycode' not valid" );
                exit();
            }
            
            unset($params['coutrycode']);
            $params['country_id']   = $country_id;
        }
        
        if( array_key_exists('provincecode',$params) && $params['provincecode'] != "" ) {
            $params['area'] = $params['provincecode'];
            unset($params['provincecode']);
        }
        
        if ($form->isValid ( $params ) ) {
            if( $params['status'] == false ) {
                $params['status'] = 'disabled';    
            }
            $customer   = Customers::findWithUuid($uuid);
            if( empty($customer) ) {
                return false;
            }
            $customerid = $customer['customer_id'];
            
            // $data       = explode('/', $params['birthdate']);
            // list( $gg, $mm, $yyyy )  = $data;
            // $params['birthdate']    = $yyyy.'-'.$mm.'-'.$gg;
            
            foreach( $customer as $name => &$value ) {
                if( array_key_exists($name, $params) ) {
                    $value  = $params[$name];
                }
            }
            
            $fields = $params;
            unset( $fields['address'] );
            unset( $fields['contact'] );
            
            Customers::saveAll( $customerid, $fields );

            $address    = array();
            $address['address']      = $params['address'];
            $address['city']        = $params['city'];
            $address['code']        = $params['code'];
            $address['area']        = $params['area'];
            $address['region_id']   = $params['regionid'];
            $address['country_id']  = $params['country_id'];
            $address['customer_id'] = $customerid;
            if( $params['addressid'] != 0 ) {
                Addresses::AddNew($address, $params['addressid']);
            } else {
                Addresses::AddNew($address);
            }
            
            
            if( array_key_exists('contacts', $params) && !empty( $params['contacts']) ) {
                foreach( $params['contacts'] as $contact ) {
                    if( $contact['contact'] == "" ) {
                        continue;
                    } 
                    $c  = array();
                    $c['contact']       = $contact['contact'];
                    $c['type_id']       = $contact['contacttypes'];
                    $c['customer_id']   = $customerid;
                    
                    if( intval($contact['idcontact']) != 0 ){
                        Contacts::AddNew($c, intval($contact['idcontact']));    
                    } else {
                        Contacts::AddNew($c);
                    }
                }
            }
            
            return true;
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