<?php
/**
 * ShineISP new Plugin Architecture
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 *
 */
class Shineisp_Registry extends Zend_Registry {
	public static function get($index) {
		return self::isRegistered($index) ? parent::get($index) : false;
	}	
}
