<?php
/**
 *
 * @version 1.0
 */
/**
 * Gravatar helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Gravatar extends Zend_View_Helper_Abstract{
	
	public function gravatar($email, $width=60) {
		
		if(!is_numeric($width)){
			$width = 60;
		}
		
		if(Shineisp_Commons_Utilities::isEmail($email)){
			return Shineisp_Commons_Gravatar::get_gravatar ( $email, $width );
		}
		
		return false;
	}
	
}
