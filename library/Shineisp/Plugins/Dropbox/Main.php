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

class Shineisp_Plugins_Dropbox_Main implements Shineisp_Plugins_Interface  {

	const CACERT_SOURCE_SYSTEM = 0;
	const CACERT_SOURCE_FILE = 1;
	const CACERT_SOURCE_DIR = 2;
	
	protected static $caCertSourceType = self::CACERT_SOURCE_SYSTEM;
	protected static $caCertSource;
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
		$em = Zend_Registry::get('em');
		if (!$this->events && is_object($em)) {
			$em->attach('invoices_pdf_created', array(__CLASS__, 'listener_invoice_upload'), 100);
			$em->attach('orders_pdf_created', array(__CLASS__, 'listener_order_upload'), 100);
		}
		return $em;
	}
	
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
				
				self::execute($file, $destinationPath, $invoice['invoice_date']);
				
				Shineisp_Commons_Utilities::log("Event triggered: invoices_pdf_created");
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
			
			Shineisp_Commons_Utilities::log("Event triggered: orders_pdf_created");
		}
		return false;
	}
	
	/**
	 * Execute the upload of the file to the dropbox service
	 * 
	 * @param string $sourcefile
	 * @return boolean
	 */
	public static function execute($sourcefile, $destinationPath, $date=null){

		if(empty($date)){
			$date = date('d-m-Y');
		}
		
		$yearoftheinvoice = date('Y',strtotime($date));
		$month_testual_invoice = date('M',strtotime($date));
		$month_number_invoice = date('m',strtotime($date));
		$quarter_number_invoice =Shineisp_Commons_Utilities::getQuarterByMonth(date('m', strtotime($date)));

		$destinationPath = str_replace("{year}", $yearoftheinvoice, $destinationPath);
		$destinationPath = str_replace("{month}", $month_number_invoice, $destinationPath);
		$destinationPath = str_replace("{monthname}", $month_testual_invoice, $destinationPath);
		$destinationPath = str_replace("{quarter}", $quarter_number_invoice, $destinationPath);
		
		if(file_exists(PUBLIC_PATH . $sourcefile )){
			self::upload(PUBLIC_PATH . $sourcefile, $destinationPath);
			return true;
		}
	}
	
	/**
	 * Check if the user has set the credencials in the administration panel
	 */
	public static function isReady() {
		$email = Settings::findbyParam ( 'dropbox_email' );
		$password = Settings::findbyParam ( 'dropbox_password' );
		if (! empty ( $email ) && ! empty ( $password )) {
			return true;
		}
		Shineisp_Commons_Utilities::log("Dropbox module: Wrong credencials");
		return false;
	}
	
	/**
	 * Set the certificate
	 * @param string $file
	 */
	public function setCaCertificateFile($file) {
		
		if(file_exists(PROJECT_PATH . "/library/Shineisp/Api/Dropbox/certificate.cer")){
			$file = PROJECT_PATH . "/library/Shineisp/Api/Dropbox/certificate.cer";
		}
		
		self::$caCertSourceType = self::CACERT_SOURCE_FILE;
		self::$caCertSource = $file;
	}
	
	/**
	 * Set the dir name of the certificate
	 * @param string $dir
	 */
	public function setCaCertificateDir($dir) {
		self::$caCertSourceType = self::CACERT_SOURCE_DIR;
		self::$caCertSource = $dir;
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
		
		if (! self::$loggedIn)
			self::login ();
		
		$data = self::request ( 'https://www.dropbox.com/home' );
		$token = self::extractToken ( $data, 'https://dl-web.dropbox.com/upload' );
		
		$postData = array ('plain' => 'yes', 'file' => '@' . $source, 'dest' => $remoteDir, 't' => $token );
		$data = self::request ( 'https://dl-web.dropbox.com/upload', true, $postData );
		if (strpos ( $data, 'HTTP/1.1 302 FOUND' ) === false)
			throw new Exception ( 'Upload failed!' );
	}
	
	/**
	 * Login in the dropbox account
	 * 
	 * @return boolean
	 * @throws Exception
	 */
	protected static function login() {
		$data = self::request ( 'https://www.dropbox.com/login' );
		$token = self::extractTokenFromLoginForm ( $data );
		
		$email = Settings::findbyParam ( 'dropbox_email' );
		$password = Settings::findbyParam ( 'dropbox_password' );
		
		$postData = array ('login_email' => $email, 'login_password' => $password, 't' => $token );
		$data = self::request ( 'https://www.dropbox.com/login', true, $postData );
		
		if (stripos ( $data, 'location: /home' ) === false)
			throw new Exception ( 'Login unsuccessful.' );
		
		self::$loggedIn = true;
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
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, true );
		switch (self::$caCertSourceType) {
			case self::CACERT_SOURCE_FILE :
				curl_setopt ( $ch, CURLOPT_CAINFO, self::caCertSource );
				break;
			case self::CACERT_SOURCE_DIR :
				curl_setopt ( $ch, CURLOPT_CAPATH, self::caCertSource );
				break;
		}
		curl_setopt ( $ch, CURLOPT_HEADER, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if ($post) {
			curl_setopt ( $ch, CURLOPT_POST, $post );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
		}
		
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
	
	/**
	 * Extract the login token from html form
	 * 
	 * @param string $html
	 * @throws Exception
	 * @return unknown
	 */
	protected static function extractTokenFromLoginForm($html) {
		// <input type="hidden" name="t" value="UJygzfv9DLLCS-is7cLwgG7z" />
		if (! preg_match ( '#<input type="hidden" name="t" value="([A-Za-z0-9_-]+)" />#', $html, $matches ))
			throw new Exception ( 'Cannot extract login CSRF token.' );
		return $matches [1];
	}
	
	/**
	 * Extract the token
	 * 
	 * @param string $html
	 * @param string $formAction
	 * @throws Exception
	 * @return unknown
	 */
	protected static function extractToken($html, $formAction) {
		if (! preg_match ( '/<form [^>]*' . preg_quote ( $formAction, '/' ) . '[^>]*>.*?(<input [^>]*name="t" [^>]*value="(.*?)"[^>]*>).*?<\/form>/is', $html, $matches ) || ! isset ( $matches [2] ))
			throw new Exception ( "Cannot extract token! (form action=$formAction)" );
		return $matches [2];
	}

}
