<?php
/**
 * Encrypt and Decrypt a string using a password
 * @author guest.it srl
 *
 */
class Shineisp_Commons_Hasher {
	/**
	 * Create the salt code
	 * @return string
	 */
	public static function createSalt(){
		$arrChars   = array();
		$saltLength = 28;
		
		while ( count ( $arrChars ) < $saltLength ) {
			$i = rand(0, 40);
			$arrChars[$i] = $i;
		}
		
		asort($arrChars);
		
		return implode(',', $arrChars);
	}
	
	/**
	 * Get the salt code  
	 * @return boolean or string
	 */
	public static function getSalt() {
		$filename = APPLICATION_PATH . "/configs/config.xml";

		// Check the existence of the config.xml file
		if (file_exists ($filename)) {

			// Load the config file
			$xml = simplexml_load_file ( $filename );
			if(empty($xml->config->saltpattern)){
				// create a salt pattern and save it in the config file
				self::resetSalt();
				
				// reload the config file
				$xml = simplexml_load_file ( $filename );
			}else{
				return preg_split('/,\s*/', $xml->config->saltpattern);
			}
			
			return preg_split('/,\s*/', $xml->config->saltpattern);
		}else{
			throw new Exception("Error on reading the xml file in " . APPLICATION_PATH . "/configs/config.xml <br/>Please check the folder permissions");
		}
	}
	
	/**
	 * Create the salt code
	 * A salt code is a random set of bytes of a 
	 * fixed length that is added to 
	 * the input of a hash algorithm.
	 */
	public static function resetSalt() {
		
		$saltpattern = self::createSalt();
		
		$filename = APPLICATION_PATH . "/configs/config.xml";
		if (file_exists ($filename)) {
			$xml = simplexml_load_file ( $filename );
			if(empty($xml->config->saltpattern)){
				$config = $xml->config;
				$config->addChild('saltpattern', $saltpattern);
			}else{
				$xml->config->saltpattern = $saltpattern;
			}
			
			// Get the xml string
			$xmlstring =$xml->asXML();
				
			// Prettify and save the xml configuration
			$dom = new DOMDocument();
			$dom->loadXML($xmlstring);
			$dom->formatOutput = true;
			$formattedXML = $dom->saveXML();
			
			// Save the config xml file
			if(@$dom->save(APPLICATION_PATH . "/configs/config.xml")){
				return true;
			}else{
				throw new Exception("Error on saving the xml file in " . APPLICATION_PATH . "/configs/config.xml <br/>Please check the folder permissions");
			}
			
		}else{
			throw new Exception('There was a problem to save data in the config.xml file. Permission file problems?');
		}
	}	

	public function hash_string($password, $salt = FALSE) {
        $salt_pattern = self::getSalt();

		if ($salt === FALSE) {
			// Create a salt seed, same length as the number of offsets in the pattern
			$salt = substr(self::hash(uniqid(NULL, TRUE)), 0, count($salt_pattern));
		}

		// Password hash that the salt will be inserted into
		$hash = self::hash($password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($salt_pattern as $offset)
		{
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	}

	public function unhash_string($password)
	{
        $salt_pattern   = self::getSalt();
		$clean_password = '';

        $password = str_split($password, 1);

		foreach ($salt_pattern as $i => $offset)
		{
			// Find salt characters, take a good long look...
			unset($password[$offset + $i]);
		}

        return implode($password);
	}


	private function hash($str)
	{
		return hash('sha1', $str);
	}

	public function find_salt($password)
	{
        $salt_pattern = self::getSalt();
		$salt = '';

		foreach ($salt_pattern as $i => $offset)
		{
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	}

}