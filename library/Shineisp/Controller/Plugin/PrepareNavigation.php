<?php
class Shineisp_Controller_Plugin_PrepareNavigation extends Zend_Controller_Plugin_Abstract {
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper ( 'ViewRenderer' );
		$viewRenderer->initView ();
		$view = $viewRenderer->view;
		
		$container = Zend_Registry::get ( 'navigation' );
		
		foreach ( $container->getPages () as $page ) {
			foreach ( $page->getPages () as $subpage ) {
				foreach ( $subpage->getPages () as $subsubpage ) {
					$uri = $subsubpage->getHref ();
					if ($uri === $request->getRequestUri ()) {
						$subsubpage->setActive(true);
					}
				}
			}
		}
		$view->navigation ( $container );
	}
}