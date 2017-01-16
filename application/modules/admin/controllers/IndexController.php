<?php

/**
 * IndexController
 * 
 * @author 
 * @version 
 */

class Admin_IndexController extends Shineisp_Controller_Admin {
	
    
    public function preDispatch() {
        $this->getHelper ( 'layout' )->setLayout ( 'blank' );
    }	
    
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'admin' ) );
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		if ($auth->hasIdentity ()) {
			$this->view->show_dashboard = true;
			$this->view->user = $auth->getIdentity();
			$this->getHelper ( 'layout' )->setLayout ( '1column' );
			
			$graph = new Shineisp_Commons_Morris();

			// Get the total of the revenues per year
			$graphdata = $graph->setType('Area')
							    ->setData(Orders::prepareGraphData(array(), 'year'))
								->setElement('yeargraph')
								->setXkey('xdata')
								->setLabels(array($translator->translate('Net Revenue (Taxable Income less Costs)')))
								->setOptions(array('lineColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
								->plot();
			
			$this->view->placeholder ( "admin_endbody" )->append ($graphdata);

			// Get the total of the revenues per quarter of year
			$graphdata = $graph->setType('Area')
							    ->setData(Orders::prepareGraphData(range(2011, date('Y')), 'quarter'))
								->setElement('quartergraph')
								->setXkey('xdata')
								->setLabels(array($translator->translate('Net Revenue (Taxable Income less Costs)')))
								->setOptions(array('lineColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
								->plot();
			
			$this->view->placeholder ( "admin_endbody" )->append ($graphdata);

			// Get the total of the revenues per months
			$graphdata = $graph->setType('Bar')
							    ->setData(Orders::prepareGraphData(array(), 'month'))
								->setElement('monthgraph')
								->setXkey('xdata')
								->setLabels(array($translator->translate('Net Revenue (Taxable Income less Costs)')))
								->setOptions(array('barColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
								->plot();
			
			$this->view->placeholder ( "admin_endbody" )->append ($graphdata);
			
		} else {
			$this->_helper->redirector ( 'index', 'login', 'admin' ); // back to login page
		}
	}
	
	/**
	 * Create a new widget for the dashboard
	 */
	public function widgetsAction(){
		$auth = Zend_Auth::getInstance ();
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'admin' ) );
		
		$id = $this->getRequest()->getParam('id', 'widget_' . rand());
		$icon = $this->getRequest()->getParam('icon', 'fa fa-file');
		$type = $this->getRequest()->getParam('type');
		$title = $this->getRequest()->getParam('title');
		$buttons = array();
		
		if(!empty($id)){
			$widget = new Shineisp_Commons_Widgets();
			$widget->setId($id);
			
			// Ajax load of the widgets

			// Get only the new orders
			if($type == "new_order_widget"){
			
				$records = Orders::Last(array(Statuses::id("tobepaid", "orders")));
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/orders/edit/id/%d"));
				$widget->setBasepath('/admin/orders/')->setIdxfield($records['index'])->setButtons($buttons);
				
			// Get all the pending, processing, and paid orders to be complete 
			}elseif($type == 'processing_order_widget'){
			
				$statuses = array(Statuses::id("processing", "orders"), Statuses::id("pending", "orders"), Statuses::id("paid", "orders"));
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/orders/edit/id/%d"));
				$records = Orders::Last($statuses);
				
				$widget->setBasepath('/admin/orders/')->setIdxfield($records['index'])->setButtons($buttons);

			// Get all the services / order items next to the expiration date from -10 days to 30 days
			}elseif($type == 'recurring_services_widget'){
			
				$records = OrdersItems::getServices();
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/ordersitems/edit/id/%d"));
				$widget->setBasepath('/admin/ordersitems/')->setIdxfield($records['index'])->setButtons($buttons);
			
			// Get the last 5 tickets opened by the customers
			}elseif($type == 'last_tickets_widget'){
			
				$records = Tickets::Last(null, 5);
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/tickets/edit/id/%d"));
				$widget->setBasepath('/admin/tickets/')->setIdxfield($records['index'])->setButtons($buttons);;
			
			// get the last domain tasks to be executed
			}elseif($type == 'last_domain_tasks_widget'){
			
				$records = DomainsTasks::Last();
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/domainstasks/edit/id/%d"));
				$widget->setBasepath('/admin/domainstasks/')->setIdxfield($records['index'])->setButtons($buttons);;
				
			// get the last ISP panel tasks to be executed 
			}elseif($type == 'last_panel_tasks_widget'){
			
				$records = PanelsActions::Last();
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/panelsactions/edit/id/%d"));
				$widget->setBasepath('/admin/panelsactions/')->setIdxfield($records['index'])->setButtons($buttons);;
			
			// get the domains next the expiration date
			}elseif($type == 'expiring_domain_widget'){
				
				// Create the header table columns
				$records['fields'] = array('expiringdate' => array('label' => $translator->translate('Expiry Date')),
										   'domains' => array('label' => $translator->translate('Domain')),
										   'days' => array('label' => $translator->translate('Days left')));
				
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/domains/edit/id/%d"));
				$records['data'] = Domains::getExpiringDomains(null, 107, -1, 5);
				
				$records['index'] = "domain_id";
				$widget->setBasepath('/admin/domains/')->setIdxfield($records['index'])->setButtons($buttons);;
				
			// get the messages/notes posted by the customers within the orders
			}elseif($type == 'order_messages_widget'){
			
				$records = Messages::Last('orders');
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/orders/edit/id/%d"));
				$widget->setBasepath('/admin/orders/')->setIdxfield($records['index'])->setButtons($buttons);;
				
			// get the messages/notes posted by the customers within the domain 
			}elseif($type == 'domain_messages_widget'){
			
				$records = Messages::Last('domains');
				$buttons = array('edit' => array('label' => $translator->translate ( 'Edit' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/domains/edit/id/%d"));
				$widget->setBasepath('/admin/domains/')->setIdxfield($records['index'])->setButtons($buttons);;
				
			// get the list of your best customers 
			}elseif($type == 'customers_parade_widget'){
			
				$buttons = array('edit' => array('label' => $translator->translate ( 'Show' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/customers/edit/id/%d"));
				
				$records = Customers::Hitparade();
				$widget->setBasepath('/admin/customers/')->setIdxfield($records['index'])->setButtons($buttons);;
			
			// get the customer status summary
			}elseif($type == 'customer_summary_widget'){
			
				$records = Customers::summary();
			
			// get the tickets summary
			}elseif($type == 'ticket_summary_widget'){
			
				$records = Tickets::summary();
				
			// get the domains summary
			}elseif($type == 'summary_domains_widget'){
			
				$records = Domains::summary();
				
			// get the product reviews stats
			}elseif($type == 'product_reviews_widget'){
			
				$records = Reviews::summary();
				
			// get the bestseller product stats
			}elseif($type == 'bestseller_widget'){
				
				$buttons = array('edit' => array('label' => $translator->translate ( 'Show' ), 'cssicon' => 'glyphicon glyphicon-pencil', 'action' => "/admin/products/edit/id/%d"));
				
				$records = Products::summary();
				$widget->setBasepath('/admin/products/')->setIdxfield($records['index'])->setButtons($buttons);;
				
			// get the last ISP notes
			}elseif($type == 'notes_widget'){
				$user = $auth->getIdentity();
				$records = Notes::summary($user['user_id']);
				$widget->setBasepath('/admin/notes/');
				
			}else{
				die('No widget type has been selected: ' . $type);
			}
			
			
			// Records Builtin columns. The code get the field names as header column name
			if(!empty($records['fields'])){
				foreach ($records['fields'] as $field => $column) {
					$column['alias'] = !empty($column['alias']) ? $column['alias'] : $field;
					$widget->setColumn($field, $column);
				}
			}
			
			if(!empty($records['data'])){
				
				$widget->setIcon($icon)->setLabel($title)->setRecords($records['data']);
				die($widget->create());
			}
			
		}
		
		die();
	}
	
}
