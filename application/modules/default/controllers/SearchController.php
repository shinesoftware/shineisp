<?php
/**
 * Handle the search engine of the website
 * 
 * @author shinesoftware
 *
 */
class SearchController extends Shineisp_Controller_Default {
	
	public function indexAction() {
		$ns = new Zend_Session_Namespace ();
		$search = $this->getRequest()->getParam('q');
		
		if(!empty($search)){
			$this->view->search = htmlspecialchars($search);
			$this->view->results = Products::search($search, $ns->langid, false);
		}else{
			$this->_redirect('/');
		}
	}
}

