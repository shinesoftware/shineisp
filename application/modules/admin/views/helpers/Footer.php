<?php
/**
 *
 * @version 
 */
/**
 * Footer helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Admin_View_Helper_Footer extends Zend_View_Helper_Abstract{
	
	public function footer() {
		return "<p><a href='http://www.shineisp.com' target='_blank'>ShineISP</a> is a GPL Project</p>";
	}
	
}
