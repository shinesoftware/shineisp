<?php

class WikiController extends Shineisp_Controller_Default {
	protected $wiki;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$registry = Shineisp_Registry::getInstance ();
		$this->wiki = new Wiki ();
		$this->translator = $registry->Zend_Translate;
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	public function indexAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$request = $this->getRequest ();
		$arrSort = array ();
		$params ['search'] = array ();
		$form = new Default_Form_WikisearchForm ( array ('action' => '/wiki', 'method' => 'post' ) );
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the values posted
			$parameters = $form->getValues ();
			if (! empty ( $parameters ['topic'] )) {
				$params ['search'] ['w.content'] ['method'] = "andWhere";
				$params ['search'] ['w.content'] ['criteria'] = "w.content like ?";
				$params ['search'] ['w.content'] ['value'] = "%" . htmlspecialchars ( $parameters ['topic'] ) . "%"; // Do not show the expired domain as default
			} else {
				$params ['search'] = array ();
			}
		}
		
		$page = ! empty ( $page ) && is_numeric ( $page ) ? $page : 1;
		$data = $this->wiki->findAll ( "w.wiki_id, w.subject as subject, w.creationdate as creationdate, w.content as content, w.uri as uri, wc.category_id as category_id, wc.category as category", $page, $NS->recordsperpage, $arrSort, $params ['search'] );
		
		$this->view->form = $form;
		$this->view->title = $this->translator->translate("Wiki Help Guide");
		$this->view->description = $this->translator->translate("Here you can see the wiki guides list.");
		$this->view->wiki = $data;
		$this->_helper->viewRenderer ( 'index' );
	}
	
	public function helpAction() {
		$uri = $this->getRequest ()->getParam ( 'uri' );
		$ns = new Zend_Session_Namespace ();
		
		if (! empty ( $uri )) {
			$uri = Shineisp_Commons_UrlRewrites::format($uri);
			$data = $this->wiki->getPostbyUri ( $uri, "w.wiki_id, w.subject as subject, w.views as views, w.creationdate as creationdate, w.content as content, w.metakeywords as metakeywords, w.metadescription as metadescription, wc.category_id as category_id, wc.category as category" );
			if (isset ( $data [0] )) {
					
				// Set the Metatag information
				$this->view->headTitle (" | " . $data [0] ['subject'] );
				if (! empty ( $data [0] ['metakeywords'] )) {
					$this->view->headMeta ()->setName ( 'keywords', $data [0] ['metakeywords'] );
				}
				
				if (! empty ( $data [0] ['metadescription'] )) {
					$this->view->headMeta ()->setName ( 'description', $data [0] ['metadescription'] );
				}				
				
				// Getting the Products
				$products = Wikilinks::getProductsAttached($data [0] ['wiki_id'], $ns->langid);
				if(count($products) > 0){
					$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'wiki/products_reference.phtml', array ('products' => $products) ) );
					$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
				}		

				// Update the counter
				Wiki::update_views($data [0] ['wiki_id']);
				
				$data [0] ['content'] = Shineisp_Commons_Contents::chkModule($data[0]['content']);
				$data [0] ['content'] = Shineisp_Commons_Contents::chkCmsBlocks ( $data [0] ['content'], $ns->lang );
				
				// Send the content to the page
				$this->view->headertitle = $this->translator->translate('Wiki page');
				$this->view->post = $data [0];
			}
		}
	}
}