<?php
/**
 * ShineISP Invoices
 *
 */
class Shineisp_Invoice extends Zend_View {
	public function __construct($config = array()) {
		parent::__construct($config);

		$this->setScriptPath(realpath(APPLICATION_PATH . '/../public').'/skins/commons/invoices/');
		$this->assign('translator', Shineisp_Registry::getInstance ()->Zend_Translate);
		
		require_once(PROJECT_PATH . '/library/html2pdf/html2pdf.class.php');
	}
}
	