<?php

/**
 * Ordersitems
 * Manage the orders items table
 * @version 1.0
 */

class Admin_OrdersitemsController extends Shineisp_Controller_Admin {
	
	protected $details;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->details = new OrdersItems ();
		$registry = Shineisp_Registry::getInstance ();
		$this->translator = $registry->Zend_Translate;
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if(!is_numeric($id)){
			$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'Unable to find the order item selected.' ), 'status' => 'danger' ) );
		}
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( "/admin/ordersitems/edit/id/$id" );
	}
	
	/**
	 * confirmAction
	 * Ask to the user a confirmation before to execute the task
	 * @return null
	 */
	public function confirmAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/delete/id/$id";
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete this item?' );
				$this->view->description = $this->translator->translate ( 'If you delete this order all the data will no longer be available.' );
				
				$record = $this->details->find ( $id, "description", true );
				$this->view->recordselected = $record [0] ['description'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'danger' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the order
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ();
		$id = $this->getRequest ()->getParam ( 'id' );
		
		if (is_numeric ( $id )) {
			$details = OrdersItems::find ( $id, "order_id", true );
			if (! empty ( $details [0] ['order_id'] )) {
				$record = Doctrine::getTable ( 'OrdersItems' )->find ( $id )->delete ();
				$orderID = $details [0] ['order_id'];
				return $this->_helper->redirector ( 'edit', 'orders', 'admin', array ('id' => $orderID ) );
			}
			return $this->_helper->redirector ( 'list', 'orders', 'admin' );
		}
		return $this->_helper->redirector ( 'list', 'orders', 'admin' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/ordersitems/process' );
		
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/ordersitems/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/ordersitems/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = OrdersItems::getAllInfo($id);
			if (! empty ( $rs )) {

				// Load the domains attached to the order
				$form->getElement ( 'domains_selected' )->setMultiOptions ( OrdersItemsDomains::getList ( $id ) );
				
				$rs ['date_start'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['date_start'] );
				$rs ['date_end'] = ! empty ( $rs ['date_end'] ) ? Shineisp_Commons_Utilities::formatDateOut ( $rs ['date_end'] ) : "";
				
				$form->populate ( $rs );

				// send the order id in the view
				$this->view->order_id = $rs['order_id'];
				
				// send the customer id in the view
				$this->view->customer_id = $rs['Orders']['customer_id'];
				
				// Check if the product is a hosting plan
				$this->view->isHosting = !empty($rs['Products']['type']) && $rs['Products']['type'] == "hosting" ? true : false;
				
				// Create the buttons in the edit form
				$this->view->buttons[] = array("url" => "/admin/ordersitems/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
				$this->view->buttons[] = array("url" => "/admin/services/edit/id/$id", "label" => $this->translator->translate('Service'), "params" => array('css' => null));
				$this->view->buttons[] = array("url" => "/admin/orders/edit/id/" . $rs['order_id'], "label" => $this->translator->translate('Order'), "params" => array('css' => null));
				$this->view->buttons[] = array("url" => "/admin/customers/edit/id/" . $rs['Orders']['customer_id'], "label" => $this->translator->translate('Customers'), "params" => array('css' => null));
				
			}else{
				$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'Unable to find the order item selected.' ), 'status' => 'danger' ) );
			}
		}else{
			$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'Unable to find the order item selected.' ), 'status' => 'danger' ) );
		}
		
		
		
		$this->view->title = $this->translator->translate("Service edit");
		$this->view->description = $this->translator->translate("Here you can edit the service selected");
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/ordersitems/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/ordersitems/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/ordersitems/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'orders', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			
			$params = $form->getValues ();
			try {
				// Save all
				OrdersItems::saveAll($params ['detail_id'], $params);
				
				$this->_helper->redirector ( 'edit', 'ordersitems', 'admin', array ('id' => $params ['detail_id'], 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'danger' ) );
			}
		} else {
			$this->view->form = $form;
			$this->view->title = "Service edit";
			$this->view->description = "Here you can edit the service selected";
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return form
	 */
	private function getForm($action) {
		$form = new Admin_Form_OrdersItemsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * 
	 * Create the action
	 */
	public function doAction() {
		$id = $this->getRequest ()->getParam('id');
		$execute = $this->getRequest ()->getParam('execute');

		if(is_numeric($id) && !empty($execute)){
			
			// Get the service information
			$service = OrdersItems::getAllInfo($id, "o.customer_id, oi.parameters");
			$parameters = $service['parameters'];
			$customer_id = $service['Orders']['customer_id'];
			
			PanelsActions::Addtask($customer_id, $id, $execute, $parameters);
			
			$this->_helper->redirector ( 'edit', 'ordersitems', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		}
		
		$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'Service item not found.' ), 'status' => 'danger' ) );
	}
	
	
	/**
	 * Update the Configuration of the Hosting Plan
	 * 
	 * 
	 * Update the configuration of the service selected
	 * with the new parameters of the service.
	 * 
	 * IMPORTANT:
	 * We have to sync the parameters with the Isp Panel selected
	 */
	public function updateconfAction() {
		$id = $this->getRequest ()->getParam('id');

		if(is_numeric($id)){
			// Get the service information
			$service = OrdersItems::getAllInfo($id, "product_id");
			if(!empty($service['product_id'])){
				
				// Get the system parameters/attributes of the service selected 
				$sysattributes = ProductsAttributes::getSysAttributes($service['product_id']);
				
				// Update the parameters in the service order detail
				OrdersItems::updateSysParameters($id, $sysattributes);
			}
			
			$this->_helper->redirector ( 'edit', 'ordersitems', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		}else{
			$this->_helper->redirector ( 'list', 'orders', 'admin', array ('mex' => $this->translator->translate ( 'Service item not found.' ), 'status' => 'danger' ) );
		}
	}

}
