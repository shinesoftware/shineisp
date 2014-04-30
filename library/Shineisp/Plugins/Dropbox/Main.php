<?php
/**
 * Dropbox Uploader
 *
 * Copyright (c) 2009 Jaka Jancar
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
 * @author Jaka Jancar [jaka@kubje.org] [http://jaka.kubje.org/]
 * @author Shine Software Italy [http://www.shinesoftware.com]
 * @version 2.0 
 */
require_once "lib/Dropbox/autoload.php";

class Shineisp_Plugins_Dropbox_Main implements Shineisp_Plugins_Interface  {

	protected static $loggedIn = false;
	protected static $appInfo;
	protected static $webAuth;
	public $events;

		
	/**
	 * Constructor of the class
	 *
	 * @param $email string       	
	 * @param $password string|null       	
	 * @throws Exception
	 */
	public function __construct() {
		
		// Check requirements
		if (! extension_loaded ( 'curl' )){
			Shineisp_Commons_Utilities::log("Dropbox module: Dropbox requires the cURL extension.");
			throw new Exception ( 'Dropbox requires the cURL extension.' );
		}
		
		$key = Settings::findbyParam ( 'dropbox_key' );
		$secret = Settings::findbyParam ( 'dropbox_secret' );
		
		if(!empty($key) && !empty($secret)){
			self::$appInfo = Dropbox\AppInfo::loadFromJson(array("key" => $key, "secret"=> $secret));
			$clientIdentifier = "shineisp/1.0";
			self::$webAuth = new Dropbox\WebAuthNoRedirect(self::$appInfo, $clientIdentifier);
		}
		
	}
	
	public static function authorize(){
		$token = Settings::findbyParam ( 'dropbox_token' );
		
		if(!$token){
			$authorizeUrl = self::getWebAuth()->start();
			header("Location: $authorizeUrl");
		}
	}
	
	private static function getWebAuth()
	{
		$authorizeUrl = self::$webAuth->start();
		header("Location: $authorizeUrl");
	}
	
	/**
	 * Execute the upload of the file to the dropbox service
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
                Shineisp_Commons_Utilities::log("Source file has been not found in $sourcefile ", "plugin_dropbox.log");
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
		$key = Settings::findbyParam ( 'dropbox_key' );
		$secret = Settings::findbyParam ( 'dropbox_secret' );
		if (! empty ( $key ) && ! empty ( $secret )) {
			return true;
		}
		Shineisp_Commons_Utilities::log("Dropbox module: Wrong credentials", "plugin_dropbox.log");
		return false;
	}
	
	
	/**
	 * Login in the dropbox account
	 * 
	 * @return boolean
	 * @throws Exception
	 */
	protected static function login() {
	    try{
            
            // get the authcode from the database
            $authcode = Settings::findbyParam ( 'dropbox_authcode' );
            $token = Settings::findbyParam ( 'dropbox_token' );
            
            if(!empty($token)){
            	echo $token;
            	die;
            	self::$loggedIn = true;
            	return true;
            }
            
            // if the authcode is set but the access token is not already set ...
            if(!empty($authcode)){
            	list($accessToken, $dropboxUserId) = self::$webAuth->finish($authcode);
            	if($accessToken){
            		Settings::saveSetting('dropbox_token', $accessToken); // save the token
            		self::$loggedIn = true;
            		return true;
            	}
            }
             
           	self::authorize();
            self::$loggedIn = false;
            
		}catch(Exception $e){
			self::$loggedIn = false;
            Shineisp_Commons_Utilities::log(__METHOD__ . " " . $e->getMessage(), "plugin_dropbox.log");
        }
	}
	
	
	/**
	 * Upload the file in the dropbox service account
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
				Shineisp_Commons_Utilities::log("Dropbox module: File '$source' too large ($filesize bytes).");
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
	
			$token = Settings::findbyParam ( 'dropbox_token' );
			
			if(empty($token)){
				self::login ();
			}
	
			$postData = array ('file' => $source, 'dest' => $remoteDir );
			$data = self::request ( 'https://api-content.dropbox.com/1/files_put/sandbox', true, $postData );
			if(!empty($data['path'])){
				Shineisp_Commons_Utilities::log('File uploaded at ' . $data['path'], "plugin_dropbox.log");
			}else{
				Shineisp_Commons_Utilities::log('Upload failed', "plugin_dropbox.log");
			}
	
		}catch(Exception $e){
			Shineisp_Commons_Utilities::log(__METHOD__ . " " . $e->getMessage(), "plugin_dropbox.log");
		}
	
	}
	
	/**
	 * Send the request to the Dropbox service
	 * 
	 * @param string $url
	 * @param boolean $post
	 * @param array $postData
	 * @throws Exception
	 * @return mixed
	 */
	protected static function request($url, $post = false, $postData = array()) {
		try{
			            
			$token = Settings::findbyParam ( 'dropbox_token' );
			$dropbox = new Dropbox\Client ($token, 'shineisp', null, self::$appInfo->getHost() );
			
			$fh = fopen($postData['file'], "rb");
			$result = $dropbox->uploadFile($postData['dest'] . basename($postData['file']), Dropbox\WriteMode::force(), $fh);
			fclose($fh);
			
			return $result;
		}catch(Exception $e){
			Shineisp_Commons_Utilities::log(__METHOD__ . " " . $e->getMessage(), "plugin_dropbox.log");
		}
	}
	
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
	 * Event Listener
	 * This event is triggered when the Invoice PDF is created
	 */
	public static function listener_invoice_upload($event) {
		$invoice = $event->getParam('invoice');
		$file = $event->getParam('file');
	
		if(is_numeric($invoice['invoice_id'])){
	
			if(self::isReady()){
	
				// get the destination path
				$destinationPath = Settings::findbyParam('dropbox_invoicesdestinationpath');
				self::execute($file, $destinationPath, $invoice['invoice_date'] . " - " . $invoice['number'] . ".pdf", $invoice['invoice_date']);
				Shineisp_Commons_Utilities::log("Event triggered: invoices_pdf_created", "plugin_dropbox.log");
				 
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
			$destinationPath = Settings::findbyParam('dropbox_ordersdestinationpath');
				
			self::execute($file, $destinationPath);
				
			Shineisp_Commons_Utilities::log("Event triggered: orders_pdf_created", "plugin_dropbox.log");
	
		}
	
		return false;
	}
	
	
}
