<?php
class CmsController extends Zend_Controller_Action {
	
	public function preDispatch() {
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	public function indexAction() {
		return $this->_helper->redirector ( 'index', 'index', 'default' );
	}
	
	/*
	 * pageAction
	 * show the page chose by the user 
	 */
	public function pageAction() {
		$var = $this->getRequest ()->getParam ( 'url' );
		$ns = new Zend_Session_Namespace ( 'Default' );
		$locale = $ns->lang;
		
		if (! empty ( $var )) {
			$page = CMSPages::findbyvar ( $var, $locale );
			
			if($page['active'] == false){
				return $this->_helper->redirector ( 'index', 'index', 'default' );
			}
			
			// Set the Metatag information
			$this->view->headTitle (" | " . $page ['title'] );
			if (! empty ( $page ['keywords'] )) {
				$this->view->headMeta ()->setName ( 'keywords', $page ['keywords'] );
			}
			if (! empty ( $page ['body'] )) {
				$this->view->headMeta ()->setName ( 'description', $page ['body'] ? Shineisp_Commons_Utilities::truncate ( strip_tags ( $page ['body'] ) ) : '-' );
			}
			
			$this->view->headertitle = $page ['title'];
			
			// Set the page content
			$this->view->data = $page;
			
			// Set the subheader
			$this->view->headerdata = $this->CreateSubHeader ( $page );
		}
		
		if (! empty ( $page ['xmllayout'] )) {
			Shineisp_Commons_Layout::updateLayout( $this->view, $page['xmllayout']);
		}
		
		if (! empty ( $page ['pagelayout'] )) {
			$this->getHelper ( 'layout' )->setLayout ("cms/" . $page ['pagelayout'] );
		}
		
		if (! empty ( $page ['layout'] )) {
			return $this->_helper->viewRenderer ( $page ['layout'] );
		} else {
			return $this->_helper->viewRenderer ( '1column' );
		}
	}
	
	/*
	 * CreateSubHeader
	 * Create a subheader menu in the cms page
	 */
	private function CreateSubHeader($page) {
		$data = array ();
		$translation = Zend_Registry::getInstance ()->Zend_Translate;
		$locale = $translation->getAdapter ()->getLocale ();
		if (strlen ( $locale ) == 2) {
			$locale = $locale . "_" . strtoupper ( $locale );
		}
		if (is_array ( $page )) {
			$pages = CMSPages::getParent ( $page ['page_id'], $locale );
			
			// Set the title of the subheader
			$data ['title'] = $page ['title'];
			
			// Set the menu of the subheader            
			if (count ( $pages ) > 0) {
				foreach ( $pages as $item ) {
					$link = ! empty ( $item ['link'] ) ? $item ['link'] : $item ['var'];
					$data ['subitems'] [] = array ('link' => $link, 'label' => $item ['title'] );
				}
			}
		}
		return $data;
	}

}