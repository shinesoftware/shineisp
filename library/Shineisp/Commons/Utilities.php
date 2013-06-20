<?php

/*
 * Shineisp_Commons_Utilities
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Commons_Utilities
* Purpose:  Class for the utilities
* -------------------------------------------------------------
*/

class Shineisp_Commons_Utilities {
	
	/**
	 * Replace a simple text link in a clickable link
	 * @param string $str
	 * @return string or boolean
	 */
	public static function makeClickableLinks($originalText) {
		return preg_replace( "/(http|https|ftp|ftps)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,6}([a-z0-9\-\.\_\/]+)?/i", '<a href="\0" target="_blank">\0</a>', $originalText );
	}
	
	/**
	 * Check if the directory is writtable
	 * @param string $path
	 */
	public static function isWritable($dirpath) {
		if(is_dir($dirpath)){
			$dummyfile = $dirpath . "/" . uniqid ( mt_rand () ) . '.tmp';
			if (! ($f = @fopen ( $dummyfile, 'w+' ))){
					return false;
			}
			fclose ( $f );
			unlink ( $dummyfile );
			return true;
		}
	}
	
	public static function getFirstFile( $dirpath, $regexp ) {
		foreach (new DirectoryIterator($dirpath) as $fileInfo) {
    		if ( $fileInfo->isDot() ) continue;
			
			if ( $fileInfo->isFile() && preg_match($regexp, $fileInfo->getFilename()) )
				return $fileInfo->getFilename();
		}
		
	}
	
	/**
	 * Get the quarter number by month number
	 * @param unknown_type $monthNumber
	 */
	public static function getQuarterByMonth($monthNumber) {
		return floor(($monthNumber - 1) / 3) + 1;
	}
	
	/**
	 * filter a text string that only contains a to z, A to Z, 0 to 9 
	 * symbol underscore or low dash "_" included 
	 * 
	 * @param string $text
	 */
	public static function format($text) {
		if(empty($text)){
			return false;
		}

		// If you need more symbols you can add them before ]
		return preg_replace("/[^a-zA-Z0-9_]+/", "", $text);
	}
	
	/**
	 * Check the database connection
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $hostname
	 * @param string $database
	 * @return boolean or string
	 */
	public static function chkdatabase($username,$password,$hostname,$database){
		try{
			$dsn = "mysql://$username:$password@$hostname/$database";
			$conn = Doctrine_Manager::connection($dsn, 'shineisp test connection');
			$conn->execute('SHOW TABLES'); # Lazy loading of the connection. If I execute a simple command the connection to the database starts.
			
			if ($conn->isConnected()) {
				// Closing the test connection
				$manager = Doctrine_Manager::getInstance();
				$manager->closeConnection($conn);
				return true;
		
			}else{
				return "There is a database connection problem, please check the credencials ($dsn)";
			}
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Unzip a archive file
	 * 
	 * @param string $filename
	 * @param string $destination
	 */
	public static function unZip($filename, $destination){
		$zip = new ZipArchive;
	    $res = $zip->open($filename);
	     if ($res === TRUE) {
	         $zip->extractTo($destination);
	         $zip->close();
	         return true;
	     } else {
	         return false;
	     }
	}
	
	/**
	 * Get the tld and the domain name splitting them within an array
	 *
	 * @param string $domainame
	 * @return array
	 */
	public static function getTld($domainame){
		if(!empty($domainame)){
			return explode('.', $domainame, 2);
		}
		
		return false;
	}
	
	/**
	 * Log file
	 * @param $str
	 * @return void
	 */
	
	public static function log($message, $filename = "errors.log", $priority=Zend_Log::INFO) {
		try{
			$logger    = new Zend_Log();
			$debug     = true;
			$debug_log = true;
			
			if(Shineisp_Main::isReady()){
				$debug     = Settings::findbyParam('debug');
				$debug_log = Settings::findbyParam('debug_log');
			}
	
			if($debug){
				$writer = new Zend_Log_Writer_Firebug();
				$logger->addWriter($writer);
				$logger->log($message, $priority);
			}
			
			if($debug_log){
				@mkdir(PUBLIC_PATH . '/logs/');
				if(is_writable(PUBLIC_PATH . '/logs/')){
					$log = fopen ( PUBLIC_PATH . '/logs/' . $filename, 'a+' );
					if(is_array($message)){
						fputs ( $log, '['.date ( 'd-m-Y H:i:s' ) . "]\n" .  var_export($message, true));
					}elseif(is_object($message)){
						fputs ( $log, '['.date ( 'd-m-Y H:i:s' ) . "]\n" .  var_export($message, true));
					}else{
						fputs ( $log, '['.date ( 'd-m-Y H:i:s' ) . "] $message\n" );
					}
					fclose ( $log );
				}else{
					$logger->log(PUBLIC_PATH . '/logs/ is not writable', Zend_Log::INFO);
				}
			}
			
		}catch (Exception $e){
			die($e->getMessage());
		}
	}
	
	/**
	 * Check if the browser is an Apple client
	 * @return boolean
	 */
	public static function isAppleClient(){
		if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) {
			return true;
		}
		return false;
	}
	
	/**
	 * Get the google latitude longitude
	 * @param string $address
	 */
	public static function getCoordinates($address){
		$address = urlencode($address);
		$uri = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=true";
		$json = @file_get_contents ( $uri );
		if(!empty($json)){
			$data = json_decode($json, true);
			if(!empty($data['status']) && $data['status'] == "OK"){
				return $data;
			}
		}
		return false;
	}
	
	/**
	 * is_valid_domain_name
	 * Check if the domain is valid 
	 * @param unknown_type $domain_name
	 */
	public static function is_valid_domain_name($url) {
		if (preg_match ( "/^[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i", $url )) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Create a little table using a multidimensional array
	 * @param array $data
	 */
	public static function array2table(array $data){
		$cell_address = "[A1,A2,D3,H3]";

		$ar_columns = array_keys($data[0]);
		$ar_rows = $data[0];
		
		$ar_addresses = explode(",", substr($cell_address, 1, -1));
		
		$html = "<table>
		  <tr>
		    <th></th>
		    <th>".implode("</td><td>", $ar_columns)."</th>
		  </tr>\n";
		
		foreach($ar_rows as $row)
		{
		  $html .= "<tr><th>".$row."</th>\n";
		
		  foreach($ar_columns as $col)
		  {
		    $cell_str = (in_array($col.$row, $ar_addresses) ? "match" : "&nbsp;");
		    $html .= "<td>".$cell_str."</td>\n";
		  }
		
		  $html .= "</tr>\n";
		}
		
		$html .= "</table>\n";

		return $html;
	}
	
	/**
	 * Convert a multidimensional array to an object
	 */
	public static function array2object($input) {
		if (is_array($input)) {
			return (object) array_map(__METHOD__, $input);
		} else {
			return $input;
		}
	}
	
	/**
	 * List all the directories
	 * @param string $directory
	 * 
	 */
	public static function dirlist($dir) {
		$dirs = array ();
		$next = 0;
		
		while ( true ) {
			$_dirs = glob ( $dir . '/*', GLOB_ONLYDIR );
			
			if (count ( $_dirs ) > 0) {
				foreach ( $_dirs as $key => $_dir )
					$dirs [] = $_dir;
			} else
				break;
			
			$dir = $dirs [$next ++];
		}
		
		return $dirs;
	}
	
	/**
	 * This function simply returns an array containing a list of a directory's contents.
	 * @param unknown_type $directory
	 */
	public static function getDirectoryList ($directory)
	{
	
		// create an array to hold directory list
		$results = array();
	
		// create a handler for the directory
		$directory_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
		foreach($directory_iterator as $filename => $path_object) {
	
			// if file isn't this directory or its parent, add it to the results
			if ($filename != "." && $filename != "..") {
				$results[] = $filename;
			}
	
		}
	
		// done!
		return $results;
	
	}
	
	/**
	 * 
	 * Check if the string is a date
	 * @param unknown_type $str
	 */
	public static function isDate($str) {
		if (! empty ( $str )) {
			$str = str_replace ( "/", "-", $str );
			$stamp = strtotime ( $str );
			
			if (! is_numeric ( $stamp )) {
				return FALSE;
			}
			
			$month = date ( 'm', $stamp );
			$day = date ( 'd', $stamp );
			$year = date ( 'Y', $stamp );
			
			if (checkdate ( $month, $day, $year )) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public static function isAjax() {
		return (isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && ($_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
	}
	
	/**
	 * Checks date if matches given format and validity of the date.
	 * Examples:
	 * <code>
	 * is_date('22.22.2222', 'mm.dd.yyyy'); // returns false
	 * is_date('11/30/2008', 'mm/dd/yyyy'); // returns true
	 * is_date('30-01-2008', 'dd-mm-yyyy'); // returns true
	 * is_date('2008 01 30', 'yyyy mm dd'); // returns true
	 * </code>
	 * @param string $value the variable being evaluated.
	 * @param string $format Format of the date. Any combination of <i>mm<i>, <i>dd<i>, <i>yyyy<i>
	 * with single character separator between.
	 */
	public static function is_valid_date($value, $format = 'dd.mm.yyyy'){
		if(strlen($value) >= 6 && strlen($format) == 10){
			 
			// find separator. Remove all other characters from $format
			$separator_only = str_replace(array('m','d','y'),'', $format);
			$separator = $separator_only[0]; // separator is first character
			 
			if($separator && strlen($separator_only) == 2){
				// make regex
				$regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
				$regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
				$regexp = str_replace($separator, "\\" . $separator, $regexp);
				if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)){
	
					// check date
					$arr=explode($separator,$value);
					$day=$arr[0];
					$month=$arr[1];
					$year=$arr[2];
					if(@checkdate($month, $day, $year))
						return true;
				}
			}
		}
		return false;
	}
	
	/*
     * formatSearchvalue
     * format the search posted values before use them in the sql query
     */
	public static function formatSearchvalue($value) {
		
		// If is a numeric
		if (is_numeric ( $value )) {
			return $value;
		}
		
		// If is a date
		if (Shineisp_Commons_Utilities::isDate ( $value )) {
			return Shineisp_Commons_Utilities::formatDateOut ( $value );
		}
		
		$value = addslashes ( $value );
		
		return $value;
	}
	
	/*
	 * Clean the tmp folder
	 */
	public static function cleantmp() {
		$seconds_old = 1800; // 30 minutes old
		$directory = PUBLIC_PATH . "/tmp";
		
		if (! $dirhandle = @opendir ( $directory ))
			return;
		
		while ( false !== ($filename = readdir ( $dirhandle )) ) {
			if ($filename != "." && $filename != "..") {
				$filename = $directory . "/" . $filename;
				if (@filemtime ( $filename ) < (time () - $seconds_old))
					@unlink ( $filename );
			}
		}
	}
	
	/*
	 * Remove empty directories
	 */
	public static function removeEmptySubFolders($path){
		$empty=true;
		
		foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file){
     		$empty = is_dir($file) && self::RemoveEmptySubFolders($file);
  		}
  		
  		return $empty && @rmdir($path);
	}
	
	// Check if the string is an email
	public static function isEmail($email) {
		return preg_match ( '|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email );
	}
	
	/**
	 * Crop a sentence
	 *
	 * @param string $strText, $intLength, $strTrail
	 * @return string $CropSentence
	 */
	public static function CropSentence($strText, $intLength, $strTrail) {
		$wsCount = 0;
		$intTempSize = 0;
		$intTotalLen = 0;
		$intLength = $intLength - strlen ( $strTrail );
		$strTemp = "";
		
		if (strlen ( $strText ) > $intLength) {
			$arrTemp = explode ( " ", $strText );
			foreach ( $arrTemp as $x ) {
				if (strlen ( $strTemp ) <= $intLength)
					$strTemp .= " " . $x;
			}
			$CropSentence = $strTemp . $strTrail;
		} else {
			$CropSentence = $strText;
		}
		
		return $CropSentence;
	}
	
	/**
	 * Converts a simpleXML element into an array. Preserves attributes and everything.
	 * You can choose to get your elements either flattened, or stored in a custom index that
	 * you define.
	 * For example, for a given element
	 * <field name="someName" type="someType"/>
	 * if you choose to flatten attributes, you would get:
	 * $array['field']['name'] = 'someName';
	 * $array['field']['type'] = 'someType';
	 * If you choose not to flatten, you get:
	 * $array['field']['@attributes']['name'] = 'someName';
	 * _____________________________________
	 * Repeating fields are stored in indexed arrays. so for a markup such as:
	 * <parent>
	 * <child>a</child>
	 * <child>b</child>
	 * <child>c</child>
	 * </parent>
	 * you array would be:
	 * $array['parent']['child'][0] = 'a';
	 * $array['parent']['child'][1] = 'b';
	 * ...And so on.
	 * _____________________________________
	 * @param simpleXMLElement $xml the XML to convert
	 * @param boolean $flattenValues      Choose wether to flatten values or to set them under a particular index. defaults to true;
	 * @param boolean $flattenAttributes  Choose wether to flatten attributes or to set them under a particular index. Defaults to true;
	 * @param boolean $flattenChildren    Choose wether to flatten children or to set them under a particular index. Defaults to true;
	 * @param string $valueKey            index for values, in case $flattenValues was set to false. Defaults to "@value"
	 * @param string $attributesKey       index for attributes, in case $flattenAttributes was set to false. Defaults to "@attributes"
	 * @param string $childrenKey         index for children, in case $flattenChildren was set to false. Defaults to "@children"
	 * @return array the resulting array.
	 */
	public static function simpleXMLToArray($xml, $flattenValues = true, $flattenAttributes = true, $flattenChildren = true, $valueKey = '@value', $attributesKey = '@attributes', $childrenKey = '@children') {
		
		$return = array ();
		
		if (! ($xml instanceof SimpleXMLElement)) {
			return $return;
		}
		$name = $xml->getName ();
		$_value = trim ( ( string ) $xml );
		if (strlen ( $_value ) == 0) {
			$_value = null;
		}
		
		if ($_value !== null) {
			if (! $flattenValues) {
				$return [$valueKey] = $_value;
			} else {
				$return = $_value;
			}
		}
		
		$children = array ();
		$first = true;
		foreach ( $xml->children () as $elementName => $child ) {
			$value = self::simpleXMLToArray ( $child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey );
			if (isset ( $children [$elementName] )) {
				if ($first) {
					$temp = $children [$elementName];
					unset ( $children [$elementName] );
					$children [$elementName] [] = $temp;
					$first = false;
				}
				$children [$elementName] [] = $value;
			} else {
				$children [$elementName] = $value;
			}
		}
		if (count ( $children ) > 0) {
			if (! $flattenChildren) {
				$return [$childrenKey] = $children;
			} else {
				$return = array_merge ( $return, $children );
			}
		}
		
		$attributes = array ();
		foreach ( $xml->attributes () as $name => $value ) {
			$attributes [$name] = trim ( $value );
		}
		if (count ( $attributes ) > 0) {
			if (! $flattenAttributes) {
				$return [$attributesKey] = $attributes;
			} else {
				$return = array_merge ( $return, $attributes );
			}
		}
		
		return $return;
	}
	
	/**
	 * Truncates text.
	 *
	 * Cuts a string to the length of $length and replaces the last characters
	 * with the ending if the text is longer than length.
	 *
	 * @param string  $text String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param string  $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 * @return string Trimmed string.
	 */
	public static function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen ( preg_replace ( '/<.*?>/', '', $text ) ) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all ( '/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER );
			$total_length = strlen ( $ending );
			$open_tags = array ();
			$truncate = '';
			foreach ( $lines as $line_matchings ) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (! empty ( $line_matchings [1] )) {
					// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
					if (preg_match ( '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings [1] )) {
						// do nothing
					// if tag is a closing tag (f.e. </b>)
					} else if (preg_match ( '/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings [1], $tag_matchings )) {
						// delete tag from $open_tags list
						$pos = array_search ( $tag_matchings [1], $open_tags );
						if ($pos !== false) {
							unset ( $open_tags [$pos] );
						}
					
		// if tag is an opening tag (f.e. <b>)
					} else if (preg_match ( '/^<\s*([^\s>!]+).*?>$/s', $line_matchings [1], $tag_matchings )) {
						// add tag to the beginning of $open_tags list
						array_unshift ( $open_tags, strtolower ( $tag_matchings [1] ) );
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings [1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen ( preg_replace ( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings [2] ) );
				if ($total_length + $content_length > $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all ( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings [2], $entities, PREG_OFFSET_CAPTURE )) {
						// calculate the real length of all entities in the legal range
						foreach ( $entities [0] as $entity ) {
							if ($entity [1] + 1 - $entities_length <= $left) {
								$left --;
								$entities_length += strlen ( $entity [0] );
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr ( $line_matchings [2], 0, $left + $entities_length );
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings [2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if ($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen ( $text ) <= $length) {
				return $text;
			} else {
				$truncate = substr ( $text, 0, $length - strlen ( $ending ) );
			}
		}
		// if the words shouldn't be cut in the middle...
		if (! $exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos ( $truncate, ' ' );
			if (isset ( $spacepos )) {
				// ...and cut the text in this position
				$truncate = substr ( $truncate, 0, $spacepos );
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if ($considerHtml) {
			// close all unclosed html-tags
			foreach ( $open_tags as $tag ) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
	/**
	 * delTree
	 * Delete the directory selected and all its subfolders
	 * @param string $dir
	 */
	public static function delTree($dir) {
		$files = glob ( $dir . '*', GLOB_MARK );
		if (is_array ( $files )) {
			foreach ( $files as $file ) {
				if (is_dir ( $file ))
					self::delTree ( $file );
				else
					unlink ( $file );
			}
			if (is_dir ( $dir ))
				@rmdir ( $dir );
		}
	}
	
	/**
	 * SendEmail
     * Smtp Configuration.
     * If you would like to use the smtp authentication, you have to add 
     * the paramenters in the Setting Module of the Control Panel
     * 
	 * @param string $from
	 * @param string or array $to
	 * @param string $bcc
	 * @param string $subject
	 * @param string $body
	 * @param string $html
	 * @param string $inreplyto
	 * @param string/array $attachments
	 * @return boolean|multitype:unknown NULL
	 */
	public static function SendEmail($from, $to, $bcc = NULL, $subject, $body, $html = false, $inreplyto = NULL, $attachments = NULL, $replyto = NULL, $cc = null) {
		$transport = null;
		$config    = array ();
		
		$host = Settings::findbyParam ( 'smtp_host' );
		
		if (! empty ( $host )) {
			$username = Settings::findbyParam ( 'smtp_user' );
			$password = Settings::findbyParam ( 'smtp_password' );
			$port = Settings::findbyParam ( 'smtp_port' );
			$port = ! empty ( $port ) ? $port : 25;
			
			if (! empty ( $username ) && ! empty ( $password )) {
				$config = array ('auth' => 'login', 'username' => $username, 'password' => $password, 'port' => $port );
			}
			
			$transport = new Zend_Mail_Transport_Smtp ( $host, $config );
		}
		
		$mail = new Zend_Mail ( 'UTF-8' );
		$mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
		
		if(!empty($attachments)){
			if(is_array($attachments)){
				foreach($attachments as $attachment){
					if(file_exists($attachment)){
						$filename = basename($attachment);
						
						// Get the content of the file
						$content = file_get_contents($attachment);
	
						// Create the attachment
						$zend_attachment = new Zend_Mime_Part($content);
						$zend_attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
						$zend_attachment->encoding    = Zend_Mime::ENCODING_BASE64;
						$zend_attachment->filename    = $filename;
						$mail->addAttachment($zend_attachment);
					}
				}
			}else{
				if(file_exists($attachments)){
					$filename = basename($attachments);
						
					// Get the content of the file
					$content = file_get_contents($attachments);
					
					// Create the attachment
					$zend_attachment = new Zend_Mime_Part($content);
					$zend_attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
					$zend_attachment->encoding    = Zend_Mime::ENCODING_BASE64;
					$zend_attachment->filename    = $filename;
					$mail->addAttachment($zend_attachment);
				}
			}
		}
		
		if(!empty($inreplyto)){
			$mail->addHeader("In-Reply-To", $inreplyto);
		}
		
		if(!empty($replyto)){
			$mail->setReplyTo($replyto);
		}

		// If the body of the message contains the HTML tags
		// we have to override the $html variable in order to send the html message by email
		
		if(self::isHtml($body)){
			$html = true;
		}
		
		if ($html) {
			$mail->setBodyHtml ( $body, null, Zend_Mime::ENCODING_8BIT);
		} else {
			$mail->setBodyText ( $body);
		}

		if ( is_array($from ) ) {
			$mail->setFrom ( $from['email'], $from['name'] );	
		} else {
			$mail->setFrom ( $from );
		}
		
		// If the $to is a group of emails addresses
		if(is_array($to)){
			foreach ($to as $recipient){
				$mail->addTo ( $recipient );
			}
		}else{
			$mail->addTo ( $to );
		}
		
		
		if (! empty ( $bcc )) {
			if ( is_array($bcc) && count($bcc) > 0 ) {
				foreach ( $bcc as $b ) {
					$mail->addBcc ( $b );	
				}
			} else {
				$mail->addBcc ( $bcc );
			}
		}

		if (! empty ( $cc )) {
			if ( is_array($cc) && count($cc) > 0 ) {
				foreach ( $cc as $c ) {
					$mail->addCc ( $c );	
				}
			} else {
				$mail->addCc ( $cc );
			}
		}
		
		$mail->setSubject ( $subject );
		
		try {
			
			$mail->send ( $transport );
			
			// All good, log to DB
			if(is_array($to)){
				
				foreach ($to as $recipient){
					// get customer_id
					$Customers = Customers::findbyemail($recipient);
					if ( is_object($Customers) && is_object($Customers->{0}) && isset($Customers->{0}->customer_id) ) {
						$customerId = $Customers->{0}->customer_id;
					}
					
					$EmailsTemplatesSends = new EmailsTemplatesSends();
					$EmailsTemplatesSends->customer_id = $customerId;         
					$EmailsTemplatesSends->fromname    = (is_array($from) && isset($from['name']))  ? $from['name']  : '';    
					$EmailsTemplatesSends->fromemail   = (is_array($from) && isset($from['email'])) ? $from['email'] : $from;
					$EmailsTemplatesSends->subject     = $subject;
					$EmailsTemplatesSends->recipient   = $recipient;
					$EmailsTemplatesSends->cc          = (is_array($cc))  ? implode(',', $cc)  : $cc;
					$EmailsTemplatesSends->bcc         = (is_array($bcc)) ? implode(',', $bcc) : $bcc;
					$EmailsTemplatesSends->html        = $html;
					$EmailsTemplatesSends->text        = $body;
					$EmailsTemplatesSends->date        = date('Y-m-d H:i:s');
					$EmailsTemplatesSends->save();

					// log the data
					Shineisp_Commons_Utilities::log("An email has been sent to $to", 'notice.log');
				}
			}else{
				// get customer_id
				$Customers = Customers::findbyemail($to);
				if ( is_object($Customers) && is_object($Customers->{0}) && isset($Customers->{0}->customer_id) ) {
					$customerId = $Customers->{0}->customer_id;
				}
				
				$EmailsTemplatesSends = new EmailsTemplatesSends();
				$EmailsTemplatesSends->customer_id = $customerId;         
				$EmailsTemplatesSends->fromname    = (is_array($from) && isset($from['name']))  ? $from['name']  : '';    
				$EmailsTemplatesSends->fromemail   = (is_array($from) && isset($from['email'])) ? $from['email'] : $from;
				$EmailsTemplatesSends->subject     = $subject;
				$EmailsTemplatesSends->recipient   = $to;
				$EmailsTemplatesSends->cc          = (is_array($cc))  ? trim(implode(',', $cc),',')  : $cc;
				$EmailsTemplatesSends->bcc         = (is_array($bcc)) ? trim(implode(',', $bcc),',') : $bcc;
				$EmailsTemplatesSends->html        = $html;
				$EmailsTemplatesSends->text        = $body;
				$EmailsTemplatesSends->date        = date('Y-m-d H:i:s');
				$EmailsTemplatesSends->save();
				
				// log the data
				Shineisp_Commons_Utilities::log("An email has been sent to $to", 'notice.log');
				
			}
			return true;
		} catch ( Exception $e ) {
			
			// log the data
			Shineisp_Commons_Utilities::log($e->getMessage ());
			die($e->getMessage ());
			return array ('email' => $to, 'message' => $e->getMessage () );
		}
		return false;
	}
	
	/*
	 *  getEmailTemplate
	 *  Get the email template from database, if missing, try to load from filesystem and save to database
	 */
	public static function getEmailTemplate($template, $language_id = null) {
		$fallbackLocale = "en_US";
		$subject = "";
		$locale  = Shineisp_Registry::get ( 'Zend_Locale' )->toString();
		
		if(empty($language_id)){
			$language_id = Languages::get_language_id($locale);
		}else{
			$locale = Languages::get_locale($language_id);
		}
		
		$EmailTemplate = EmailsTemplates::findByCode($template, null, false, $language_id);

		// Template missing from DB. Let's add it.
		if ( !is_object($EmailTemplate) || !isset($EmailTemplate->EmailsTemplatesData) || !isset($EmailTemplate->EmailsTemplatesData->{0}) || !isset($EmailTemplate->EmailsTemplatesData->{0}->subject) ) {
			$filename = PUBLIC_PATH . "/languages/emails/".$locale."/".$template.".htm";
			
			// Check if the file exists
			if (! file_exists ( $filename )) {
				$filename = PUBLIC_PATH . "/languages/emails/".$fallbackLocale."/".$template.".htm";
				Shineisp_Commons_Utilities::log("This email template has not been found: $filename");
				
				// Also the fallback template is missing. Something strange is going on.....
				if (! file_exists ( $filename )) {
					Shineisp_Commons_Utilities::log("The default email template has not been found: $filename");
					return array('template' => "Template: ".$template." non trovato", 'subject' => $template);
				}
			}

			// Get the content of the file
			$body = '';
			foreach ( file ($filename) as $line ) {
				// Get the subject written in the template file
				if (preg_match ( '/<!--@subject\s*(.*?)\s*@-->/', $line, $matches )) {
					$subject = $matches [1]; // Get the subject
					$subject = trim ( $subject );
					continue;
				}
				
				// Delete all the comments
				$body .= preg_replace ( '#\{\*.*\*\}#suU', '', $line );
			}
			
			
			// TODO: properly manage ISP ID
			$isp = Shineisp_Registry::get('ISP')->toArray();

			$body = trim($body);
			$subject = trim($subject);
			
			// check if the string contains html tags and if it does not contain tags
			// means that it is a simple text. In this case add the tag "<br/>" for each return carrier
			if(!self::isHtml($body)){
				$body = nl2br($body);
			}
			
			// Store mail in DB
			$array = array(
			     'type'      => 'general'
				,'name'      => $template
				,'code'      => $template
				,'plaintext' => 0
				,'active'    => 1
				,'fromname'  => ( is_array($isp) && isset($isp['company']) ) ? $isp['company'] : 'ShineISP'        // TODO: remove this hardcoded value
				,'fromemail' => ( is_array($isp) && isset($isp['email']) )   ? $isp['email'] : 'info@shineisp.com'  // TODO: remove this hardcoded value
				,'subject'   => $subject
				,'html'      => $body
			
			);
			
			// Save the data
			EmailsTemplates::saveAll(null, $array, $language_id);
				
			// Return the email template
			return array_merge($array, array('template' => $body, 'subject' => $subject));
		}
		
		// template is numeric but there is not template in db. something strange happened. Exit.
		if ( is_numeric($template) && !is_object($EmailTemplate) ) {
			return false;
		}
		
		$email = array(
			 'subject'   => $EmailTemplate->EmailsTemplatesData->{0}->subject
			,'plaintext' => intval($EmailTemplate->plaintext)
			,'fromname'  => $EmailTemplate->EmailsTemplatesData->{0}->fromname
			,'fromemail' => $EmailTemplate->EmailsTemplatesData->{0}->fromemail
			,'cc'        => $EmailTemplate->cc
			,'bcc'       => $EmailTemplate->bcc
			,'template'  => ''
		);
		
		if ( !empty($EmailTemplate->EmailsTemplatesData->{0}->html) && !empty($EmailTemplate->EmailsTemplatesData->{0}->text) ) {
			// Both version are present
			$body = (intval($EmailTemplate->plaintext)) ? $EmailTemplate->EmailsTemplatesData->{0}->text : $EmailTemplate->EmailsTemplatesData->{0}->html;	
		} else if ( empty($EmailTemplate->EmailsTemplatesData->{0}->html) && !empty($EmailTemplate->EmailsTemplatesData->{0}->text) ) {
			// Only TEXT version
			$body = $EmailTemplate->EmailsTemplatesData->{0}->text;
		} else {
			// Only HTML version
			$body = $EmailTemplate->EmailsTemplatesData->{0}->html;
		}
		
		$email['template'] = $body;
		
		return $email;
	}

	/**
	 * Check if the string contains html tags
	 * @param unknown_type $string
	 */
	public static function isHtml($string){
		 preg_match("/<\/?\w+((\s+\w+(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/",$string, $matches);
	     if(count($matches)==0){
	        return FALSE;
	      }else{
	         return TRUE;
	      }
	}

	/**
	 * sendEmailTemplate: send an email template replacing all placeholders
	 * 
	 * TODO: GUEST - ALE - 20130531: THIS METHOD MUST BE REFACTORED
	 * 
	 */
	public static function sendEmailTemplate($recipient = null, $template = '', $replace = array(), $inreplyto = null, $attachments = null, $replyto = null, $ISP = null, $language_id = null) {
		
		// Get email template
		$arrTemplate = self::getEmailTemplate($template, $language_id);
		if ( !is_array($arrTemplate) ) {
			return false;
		}
		
		$arrReplaced = array();

		// ISP missing from arguments, try to get automatically
		$ISP = ( isset($ISP) && is_array($ISP) ) ? $ISP : ISP::getCurrentISP();
				
		// Add some mixed parameters
		$ISP['signature'] = $ISP['company']."\n".$ISP['website'];
		$ISP['storename'] = $ISP['company'];
		
		// All placeholder prefixed with "isp_" will be replaced with ISP data
		foreach ( $ISP as $k => $v ) {
			$replace['isp_'.$k] = $v;
		}
		
		// Merge original placeholder with ISP value. This is done to override standard ISP values
		$replace = array_merge($ISP, $replace);

		// Check if special placeholder :shineisp: is set. If is set and is an array, it will use it as a source of key/value
		if ( isset($replace[':shineisp:']) && is_array($replace[':shineisp:']) ) {
			if ( isset($replace[':shineisp:'][0]) ) {
				$replace[':shineisp:'] = array_merge($replace[':shineisp:'], $replace[':shineisp:'][0]);
				unset($replace[':shineisp:'][0]);
			}
			foreach ( $replace[':shineisp:'] as $k => $v ) {
				$replace[$k] = $v;
			}
			
			unset($replace[':shineisp:']);	
		}

		// Remove unneeded parameters
		unset($replace['active']);
		unset($replace['isppanel']);

		// Replace all placeholders in everything
		
		foreach ( $replace as $placeholder => $emailcontent ) {
			foreach ( $arrTemplate as $k => $v ) {
				
				// $replace contains the order header information
				if(is_string($emailcontent)){
					$arrTemplate[$k] = str_replace('['.$placeholder.']', $emailcontent, $v);
				}
			}
		}
		
		// Send the email
		$arrBCC  = array();
		$arrCC   = array();
		$arrFrom = array('email' => $arrTemplate['fromemail'], 'name' => $arrTemplate['fromname']);
		
		if ( isset($arrTemplate['bcc']) && !empty($arrTemplate['bcc']) ) {
			if (is_array($arrTemplate['bcc']) && count($arrTemplate['bcc']) > 0) {
				$arrBCC = array_merge($arrBCC, $arrTemplate['bcc']);
			} else {
				$arrBCC[] = $arrTemplate['bcc'];
			}
		}
		// Get always-bcc from Settings
		$always_send_to = Settings::findbyParam('always_send_to');
		$always_send_to = trim($always_send_to);
		if ( !empty($always_send_to) ) {
			if ( strpos($always_send_to, ',') !== false ) {
				$_bcc = explode(',',$always_send_to);
				foreach ( $_bcc as $_bccAddress ) {
					$arrBCC[] = trim($_bccAddress);	
				}	
			} else {
				$arrBCC[] = $always_send_to;		
			}	
		}
		//$arrBCC[] = $arrTemplate['fromemail']; // always BCC for sender
		$arrBCC = array_unique($arrBCC); // Remove duplicate bcc addresses
		
		if ( isset($arrTemplate['cc']) && !empty($arrTemplate['cc']) ) {
			if (is_array($arrTemplate['cc'])  && count($arrTemplate['cc']) > 0) {
				$arrCC = array_merge($arrCC, $arrTemplate['cc']);
			} else {
				$arrCC[] = $arrTemplate['cc'];
			}
		}
		
		// null recipient, send only to ISP
		$recipient = ($recipient == null) ? $ISP['email'] : $recipient;
		
	    // SendEmail    (    $from,        $to,    $bcc,                $subject,                    $body,                      $html, $inreplyto, $attachments, $replyto,    $cc ) 
		self::SendEmail ( $arrFrom, $recipient, $arrBCC, $arrTemplate['subject'], $arrTemplate['template'], !$arrTemplate['plaintext'], $inreplyto, $attachments, $replyto, $arrCC );
	}


	public static function cvsExport($recordset) {
		$cvs = "";
		@unlink ( "documents/export.csv" );
		if (! empty ( $recordset ) && is_array ( $recordset )) {
			$fp = fopen ( 'documents/export.csv', 'w' );
			$fields = array_keys ( $recordset [0] );
			$cvs = implode ( ";", $fields ) . "\n";
			
			foreach ( $recordset as $record ) {
				$cvs .= implode ( ";", $record ) . "\n";
			}
			fwrite ( $fp, $cvs );
			fclose ( $fp );
		}
		return $cvs;
	}
	
	public static function whoisInfo($domain) {
		$uri = "http://www.webservicex.net/whois.asmx/GetWhoIS?HostName=$domain";
		$client = new Zend_Http_Client ( $uri );
		try {
			return $client->request ();
		} catch ( exception $e ) {
			return $e->message ();
		}
	}

	public static function in_arrayi($needle, $haystack) {
		return in_array ( strtolower ( $needle ), array_map ( 'strtolower', $haystack ) );
	}
	
	/**
	 * Convert a date from yyyy/mm/dd formatted by the locale setting
	 *
	 * @param date $dbindata
	 * @param $format Zend_date format
	 * @return date formatted by the locale setting
	 */
	public static function formatDateOut($dbindata, $format=Zend_Date::DATE_MEDIUM, $showTime=false) {
		if (empty ( $dbindata ))
			return false;
		
		$locale = Shineisp_Registry::get('Zend_Locale');
		$date = new Zend_Date($dbindata, "yyyy-MM-dd HH:mm:ss", $locale);

		// override the preferences
		$dateformat = Settings::findbyParam('dateformat');
		
		if(!empty($dateformat)){
			$dateformat .= ($showTime) ? " HH:mm:ss" : null;
			return $date->get($dateformat);
		}else{
			$format .= ($showTime) ? " HH:mm:ss" : null;
			return $date->get($format);
		}
		
	}
	
	/**
	 * Convert a date from Zend Locale selected to yyyy/mm/dd H:i:s
	 *
	 * @param string $dboutdata
	 * @return string Y-m-d H:i:s
	 */
	public static function formatDateIn($dboutdata) {
		if (empty ( $dboutdata ))
			return null;
		
		$date = new Zend_Date($dboutdata);
		
		return $date->toString('yyyy-MM-dd HH:mm:ss');
	}
	
	/**
	 * Adding a number of days and/or months and/or years to a date
	 * 
	 * @param string $givendate
	 * @param integer $day
	 * @param integer $mth
	 * @param integer $yr
	 * @return boolean|Zend_Date
	 */
	public static function add_date($givendate, $day = 0, $mth = 0, $yr = 0) {
		if (empty ( $givendate ))
			return false;
		
		try{
			$date = new Zend_Date($givendate);

			if($day > 0){
				$date->add($day, Zend_Date::DAY);
			}
			
			if($mth > 0){
				$date->add($mth, Zend_Date::MONTH);
			}
			
			if($yr > 0){
				$date->add($yr, Zend_Date::YEAR);
			}
			
		}catch (Exception $e){
			die($e->getMessage());
		}
		return $date;
	}
		
	
	// ***************************************** START GRID CUSTOM FUNCTIONS *****************************************
	/**
	 * array_flatten
	 * Create a flat array starting from a multidimensional array
	 * @return array
	 */
	public static function array_flatten($a, $f = array()) {
		if (! $a || ! is_array ( $a ))
			return $f;
		foreach ( $a as $k => $v ) {
			if (is_array ( $v ))
				$f = self::array_flatten ( $v, $f );
			else
				$f [$k] = $v;
		}
		return $f;
	}
	
	/**
	 * search
	 * Get the value of a specific key in a multidimensional array 
	 * @var string
	 */
	public static function search($keys, $search, &$results) {
		foreach ( $search as $k => $v ) {
			if (is_array ( $v )) {
				self::search ( $keys, $v, $results );
			} elseif (in_array ( $k, $keys )) {
				$results [$k] = $v;
			}
		}
	}
	
	// ***************************************** END GRID CUSTOM FUNCTIONS *****************************************
	

	/**
	 * readfile
	 * Open file
	 * @param $filename (full path of the file)
	 * @return string
	 */
	
	public static function readfile($filename) {
		return @file_get_contents ( $filename );
	}
	
	/**
	 * Write File
	 * @param $str
	 * @return void
	 */
	
	public static function writefile($content, $folder = "documents", $filename = "dummy.txt") {
		$file = fopen ( PUBLIC_PATH . "/$folder/" . $filename, 'w+' );
		fputs ( $file, $content );
		fclose ( $file );
		return true;
	}
	
	/**
	 * Log file
	 * @param $str
	 * @return void
	 */
	
	public static function logs($str, $filename = "errors.log") {
		$log = fopen ( PUBLIC_PATH . '/logs/' . $filename, 'a+' );
		fputs ( $log, '['.date ( 'd-m-Y H:i:s' ) . "] $str\n" );
		fclose ( $log );
	}
	
	/**
	 * GenerateRandomString
	 * generate a random string
	 * @param $length
	 * @return string
	 */
	public static function GenerateRandomString($length = 8) {
		// Must be a multiple of 2 !! So 14 will work, 15 won't, 16 will, 17 won't and so on
		

		$conso = array ("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "v", "w", "x", "y", "z" );
		$vocal = array ("a", "e", "i", "o", "u" );
		$password = "";
		srand ( ( double ) microtime () * 1000000 );
		$max = $length / 2;
		for($i = 1; $i <= $max; $i ++) {
			$password .= $conso [rand ( 0, 19 )];
			$password .= $vocal [rand ( 0, 4 )];
		}
		$newpass = $password;
		return $newpass;
	}

	/**
	 * GenerateRandomPassword
	 * generate a random password
	 * @param $length
	 * @return string
	 */
	public static function GenerateRandomPassword($length = 16) {
		$chars       = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    	$password    = array();
    	$alphaLength = strlen($chars) - 1;
		
    	for ($i = 0; $i < $length; $i++) {
        	$n = mt_rand(0, $alphaLength);
        	$password[] = $chars[$n];
    	}
		
    	return implode($password);
	}


	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $array
	 * @param unknown_type $cols
	 */
	public static function columnSort($unsorted, $column) {
		$sorted = $unsorted;
		for($i = 0; $i < sizeof ( $sorted ) - 1; $i ++) {
			for($j = 0; $j < sizeof ( $sorted ) - 1 - $i; $j ++)
				if ($sorted [$j] [$column] > $sorted [$j + 1] [$column]) {
					$tmp = $sorted [$j];
					$sorted [$j] = $sorted [$j + 1];
					$sorted [$j + 1] = $tmp;
				}
		}
		return $sorted;
	}

	/**
	 * Convert from the bytes in human reading
	 * 
	 * @param long $bytes
	 * @return string
	 */
	public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}
	
	/**
	 * Convert from Megabytes to Bytes 
	 * @param integer $megabytes
	 * @return integer
	 */
	public static function MB2Bytes($megabytes)
	{
		if($megabytes > 0){
			return $megabytes * 1048576;
		}else{
			return 0;
		}
	}
	
	public static function Capitalize($str, $is_name=false) {
		// exceptions to standard case conversion
		if ($is_name) {
			$all_uppercase = '';
			$all_lowercase = 'De La|De Las|Der|Van De|Van Der|Vit De|Von|Or|And';
		} else {
			// addresses, essay titles ... and anything else
			$all_uppercase = 'Po|Rr|Se|Sw|Ne|Nw';
			$all_lowercase = 'A|And|As|By|In|Of|Or|To';
		}
		$prefixes = 'Mc';
		$suffixes = "'S";
	
		// captialize all first letters
		$str = preg_replace('/\\b(\\w)/e', 'strtoupper("$1")', strtolower(trim($str)));
	
		if ($all_uppercase) {
			// capitalize acronymns and initialisms e.g. PHP
			$str = preg_replace("/\\b($all_uppercase)\\b/e", 'strtoupper("$1")', $str);
		}
		if ($all_lowercase) {
			// decapitalize short words e.g. and
			if ($is_name) {
				// all occurences will be changed to lowercase
				$str = preg_replace("/\\b($all_lowercase)\\b/e", 'strtolower("$1")', $str);
			} else {
				// first and last word will not be changed to lower case (i.e. titles)
				$str = preg_replace("/(?<=\\W)($all_lowercase)(?=\\W)/e", 'strtolower("$1")', $str);
			}
		}
		if ($prefixes) {
			// capitalize letter after certain name prefixes e.g 'Mc'
			$str = preg_replace("/\\b($prefixes)(\\w)/e", '"$1".strtoupper("$2")', $str);
		}
		if ($suffixes) {
			// decapitalize certain word suffixes e.g. 's
			$str = preg_replace("/(\\w)($suffixes)\\b/e", '"$1".strtolower("$2")', $str);
		}
		return $str;
	}

	/*
	 * do a callback post. Used by ShineISP API
	 */
	public function doCallbackPOST($url, $params) {
		//open connection
		$ch = curl_init();
		
		$post_string = json_encode($params);
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($post_string))                                                                       
		);
		
		//execute post
		$result = curl_exec($ch);
		
		Shineisp_Commons_Utilities::logs ("POST CALLBACK: url: ".$url. " - json: ".$post_string." - result: ".$result, "api-callback.log" );
		
		//close connection
		curl_close($ch);		
	}

}