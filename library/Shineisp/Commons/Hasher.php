<?php
/**
 * Encrypt and Decrypt a string using a password
 * @author guest.it srl
 *
 */
class Shineisp_Commons_Hasher {
    public static function generateSalt() {
    	$salt_pattern = '0, 1, 2, 6, 7, 10, 11, 12, 14, 15, 20, 21, 25, 29, 32, 36, 37, 38, 39, 40';
        return preg_split('/,\s*/', $salt_pattern); 
    }

	public function hash_string($password, $salt = FALSE) {
        $salt_pattern = self::generateSalt();

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
        $salt_pattern   = self::generateSalt();
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
        $salt_pattern = self::generateSalt();
		$salt = '';

		foreach ($salt_pattern as $i => $offset)
		{
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	}

}