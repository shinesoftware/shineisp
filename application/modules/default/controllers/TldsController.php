<?php

/**
 * Tlds
 * 
 */

class TldsController extends Zend_Controller_Action {
	
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
	}	
	
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$uri = $this->getRequest ()->getParam ( 'uri' );
		
		if (! empty ( $uri )) {
			$tld = DomainsTlds::getbyTld($uri, $ns->langid);

			// If the translation has been not found load the default language
			if(empty($tld['DomainsTldsData'][0])){
				$tld = DomainsTlds::getbyTld($uri);	
			}
		}
		
    	// Set the page title
		$this->view->headTitle()->prepend ( $this->translator->_('.%s domain registration', $tld['DomainsTldsData'][0]['name'] ));
		$this->view->headMeta ()->setName ( 'keywords', $this->translator->translate('domain registration') . ", " . $this->translator->_('%s domain', "." . $tld['DomainsTldsData'][0]['name'] ));
		$this->view->headMeta ()->setName ( 'description', $this->translator->_('.%s domain registration', $tld['DomainsTldsData'][0]['name'] ) );
		
		$form = new Default_Form_DomainsinglecheckerForm ( array ('action' => '/domainschk/check', 'method' => 'post' ) );
		$form->populate(array('tld' => $tld['tld_id']));
		$this->view->form = $form;
		$this->view->tld = $tld;
		$this->view->tldslist = DomainsTlds::getList();
	}

}
