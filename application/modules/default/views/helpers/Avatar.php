<?php
/**
 *
 * @version 0.1
 */
/**
 * Avatar helper
 * Get the Avatar picture from a remote service
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Avatar extends Zend_View_Helper_Abstract {
	/*
	 * 
	 */
	public function Avatar($email, $width="50") {
		return Shineisp_Commons_Gravatar::get_gravatar($email, $width);
	}
}