<?php
/**
 * Seafile Uploader
 *
 * Copyright (c) 2013 Shine Software
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Shine Software Italy [http://www.shinesoftware.com]
 * @version 2.0 
 */

class Shineisp_Plugins_Seafile_Main implements Shineisp_Plugins_Interface  {
    protected static $token = false;
    protected static $loggedIn = false;
    protected static $cookies = array ();
    
	public $events;

	
	/**
	 * Events Registration
	 * 
	 * (non-PHPdoc)
	 * @see Shineisp_Plugins_Interface::events()
	 */

	public function events()

	{
		$em = Shineisp_Registry::get('em');

		if (!$this->events && is_object($em)) {
			$em->attach('invoices_pdf_created', array(__CLASS__, 'listener_invoice_upload'), 100);
			$em->attach('orders_pdf_created', array(__CLASS__, 'listener_order_upload'), 100);
		}

		return $em;

	}
	
	/**
	 * Constructor of the class
	 *
	 * @param $username string       	
	 * @param $password string|null       	
	 * @throws Exception
	 */
	public function __construct() {
		
		// Check requirements
		if (! extension_loaded ( 'curl' )){
			Shineisp_Commons_Utilities::log("Seafile module: Seafile requires the cURL extension.");
			throw new Exception ( 'Seafile requires the cURL extension.' );
		}
	}
	
	/**
	 * Event Listener
	 * This event is triggered when the Invoice PDF is created 
	 */
	public static function listener_invoice_upload($event) {
		$invoice = $event->getParam('invoice');
		$file = $event->getParam('file');
		
		if(is_numeric($invoice['invoice_id'])){

			if(self::isReady()){
				
				// get the destination path
				$destinationPath = Settings::findbyParam('seafile_invoicesdestinationpath');
				
				self::execute($file, $destinationPath, $invoice['invoice_date'] . " - " . $invoice['number'] . ".pdf", $invoice['invoice_date']);
				
				Shineisp_Commons_Utilities::log("Event triggered: invoices_pdf_created", "plugin_seafile.log");
             
			}

		}

		return false;
	}
	
	/**
	 * Event Listener
	 * This event is triggered when the Orders PDF is created 
	 */
	public static function listener_order_upload($event) {
		$file = $event->getParam('file');

		if(self::isReady()){
				
			// get the destination path
			$destinationPath = Settings::findbyParam('seafile_ordersdestinationpath');
			
			self::execute($file, $destinationPath);
			
			Shineisp_Commons_Utilities::log("Event triggered: orders_pdf_created", "plugin_seafile.log");

		}

		return false;
	}
	
	/**
	 * Execute the upload of the file to the seafile service
	 * 
	 * @param string $sourcefile
	 * @return boolean
	 */
	public static function execute($sourcefile, $destinationPath, $remotename=null, $date=null){
        
        try{
            if(empty($date)){
                $date = date('Y-m-d');
            }
        
            $yearoftheinvoice = date('Y',strtotime($date));
            $month_testual_invoice = date('M',strtotime($date));
            $month_number_invoice = date('m',strtotime($date));
            $quarter_number_invoice = Shineisp_Commons_Utilities::getQuarterByMonth(date('m', strtotime($date)));

            $destinationPath = str_replace("{year}", $yearoftheinvoice, $destinationPath);
            $destinationPath = str_replace("{month}", $month_number_invoice, $destinationPath);
            $destinationPath = str_replace("{monthname}", $month_testual_invoice, $destinationPath);
            $destinationPath = str_replace("{quarter}", $quarter_number_invoice, $destinationPath);

            if(file_exists(PUBLIC_PATH . $sourcefile )){
			    self::upload(PUBLIC_PATH . $sourcefile, $destinationPath, $remotename);
                return true;
            }else{
                Shineisp_Commons_Utilities::log("Source file has been not found in $sourcefile ", "plugin_seafile.log");
                return false;
            }
            
        }catch(Exception $e){
            Shineisp_Commons_Utilities::log($e->getMessage());
        }
	}
	
	/**
	 * Check if the user has set the credentials in the administration panel
	 */
	public static function isReady() {
		$url = Settings::findbyParam ( 'seafile_url' );
		$repoid = Settings::findbyParam ( 'seafile_repoid' );
		$username = Settings::findbyParam ( 'seafile_username' );
		$password = Settings::findbyParam ( 'seafile_password' );
		if (! empty ( $username ) && ! empty ( $password ) && ! empty ( $url ) && ! empty ( $repoid )) {
			return true;
		}
		Shineisp_Commons_Utilities::log("Seafile module: Empty credentials or wrong settings", "plugin_seafile.log");
		return false;
	}
	
	/**
	 * Upload the file in the seafile service account 
	 *  
	 * @param string $source
	 * @param string $remoteDir
	 * @param string $remoteName
	 * @throws Exception
	 */
	public static function upload($source, $remoteDir = '/', $remoteName = null) {
		try{
            $params = compact('source', 'remoteDir', 'remoteName');
        
            if (! is_file ( $source ) or ! is_readable ( $source ))
                throw new Exception ( "File '$source' does not exist or is not readable." );
        
            $filesize = filesize ( $source );
            if ($filesize < 0 ) {
                Shineisp_Commons_Utilities::log("Seafile module: File '$source' too large ($filesize bytes).");
                throw new Exception ( "File '$source' too large ($filesize bytes)." );
            }
        
            if (! is_string ( $remoteDir ))
                throw new Exception ( "Remote directory must be a string, is " . gettype ( $remoteDir ) . " instead." );
        
            if (is_null ( $remoteName )) {
                // intentionally left blank
            } else if (! is_string ( $remoteName )) {
                throw new Exception ( "Remote filename must be a string, is " . gettype ( $remoteDir ) . " instead." );
            } else {
                $source .= ';filename=' . $remoteName;
            }
        
            if (! self::$loggedIn){
                if(self::login ()){
                    $url = Settings::findbyParam ( 'seafile_url' );
                    $repoid = Settings::findbyParam ( 'seafile_repoid' );
                    $postData = array('operation' => 'mkdir', 'token' => self::$token, 'p' => $remoteDir);
                    $data = self::request ( "$url/api2/repos/$repoid/dir/", true, $postData );
                    
                    print_r($postData);
                    print_r($data);
                    die;
                }
            }
        }catch(Exception $e){
            Shineisp_Commons_Utilities::log($e->getMessage(), "plugin_seafile.log");
        }
        
	}
	
	/**
	 * Login in the seafile account
	 * 
	 * @return boolean
	 * @throws Exception
	 */
	protected static function login() {
	    try{
        
            $url = Settings::findbyParam ( 'seafile_url' );
            $username = Settings::findbyParam ( 'seafile_username' );
            $password = Settings::findbyParam ( 'seafile_password' );

            $postData = array ('username' => $username, 'password' => $password );
            $data = self::request ( $url . '/api2/auth-token/', true, $postData );

            $data = json_decode($data, true);
            if (!empty($data['non_field_errors'])){
                throw new Exception ($data['non_field_errors'][0]);
            }else{
                self::$loggedIn = true;
                self::$token = $data['token'];
                return true;
            }
            
            return false;
		}catch(Exception $e){
            Shineisp_Commons_Utilities::log($e->getMessage(), "plugin_seafile.log");
        }
	}
	
	/**
	 * Send the request to the Seafile service
	 * 
	 * @param string $url
	 * @param boolean $post
	 * @param array $postData
	 * @throws Exception
	 * @return mixed
	 */
	protected static function request($url, $post = false, $postData = array()) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, true );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if ($post) {
			curl_setopt ( $ch, CURLOPT_POST, $post );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
		}
		
// 		Shineisp_Commons_Utilities::log($url, "plugin_seafile.log");
// 		Shineisp_Commons_Utilities::log($postData, "plugin_seafile.log");
		
		// Send cookies
		$rawCookies = array ();
		foreach ( self::$cookies as $k => $v )
			$rawCookies [] = "$k=$v";
		$rawCookies = implode ( ';', $rawCookies );
		curl_setopt ( $ch, CURLOPT_COOKIE, $rawCookies );
		
		$data = curl_exec ( $ch );
		
		if ($data === false) {
			throw new Exception ( sprintf ( 'Curl error: (#%d) %s', curl_errno ( $ch ), curl_error ( $ch ) ) );
		}
		
		// Store received cookies
		preg_match_all ( '/Set-Cookie: ([^=]+)=(.*?);/i', $data, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match )
			self::$cookies [$match [1]] = $match [2];
		
		curl_close ( $ch );
		
		return $data;
	}

}
