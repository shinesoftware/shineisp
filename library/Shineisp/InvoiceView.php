<?php
/**
 * ShineISP Invoices
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 *
 */
class Shineisp_InvoiceView extends Zend_View {
	public function __construct($config = array()) {
		parent::__construct($config);

		$this->setScriptPath(realpath(APPLICATION_PATH . '/../public').'/skins/commons/invoices/');
		$this->assign('translator', Zend_Registry::getInstance ()->Zend_Translate);
		
		require_once('html2pdf.class.php');
	}
}
	