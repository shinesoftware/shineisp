<?php

/**
 * Admin_SubscribersController
 * Handle the Reviews of the Reviews
 * @version 1.0
 */

class Admin_ReportsController extends Shineisp_Controller_Admin {
	
	protected $session;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
	}
	
	/**
	 * indexAction
	 * Create the reports page
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/reports/show' );
	}
	
	/**
	 * showAction
	 * Create the reports page
	 */
	public function showAction() {
		$request = $this->getRequest ();
		$datatype = $request->getParam ( 'type' );
		$param = $request->getParam ( 'q' );
		$autorenew = 1;
		
		switch ($datatype) {
			case 'profitofyear' :
				if(is_numeric($param)){
					$links = $this->translator->_("Click one of these links to show the economic reports");
					$years = Invoices::getYears();
					
					foreach ($years as $year) {
						$links .= " <a href='/admin/reports/show/type/profitofyear/q/$year'>$year</a>";
					}
					
					$this->view->title = $this->translator->_("Estimate revenues for %s", $param);
					
					if(!empty($years)){
						$this->view->description = $this->translator->_("Here below the economic summary of the %s.", $param) . " " . $links;
					}else{
						$this->view->description = $this->translator->_("Here below the economic summary of the %s.", $param);
					}
					$this->view->year = $param;
					
					// Get the graph data
					$this->view->monthsummary = Invoices::graph(array($param));
					$this->view->weeksummary = Invoices::graph_week($param.'-01-01', $param . "-12-31");
					
					Invoices::getSummaryGrid($this->_helper, $param);
					PurchaseInvoices::getSummaryGrid($this->_helper, $param);
					
				}else{
					$this->_helper->redirector ( 'show', 'reports', 'admin', array ('type'=>'profitofyear', 'q' => date('Y') ) );
				}
				break;
			
			case 'tldsummarypermonth' :
				$this->view->title = $this->translator->translate("Month TLD Summary");
				$this->view->description = $this->translator->translate("In this list you can see the summary of the TLD per month.");
				$this->view->graph = array(Domains::tldSummaryPerMonth ());
				$this->view->data = array ('records' => Domains::getSummaryPerMonth ());
				break;
			
			case 'domainstats' :
				$this->view->title = $this->translator->translate("Domains Stats");
				$this->view->description = $this->translator->translate("This list shows all the costs and earnings of the domains sold grouped by tld.");
				$this->view->graph = array(Domains::earningsSummary ());
				$this->view->data = array ('records' => Domains::getSummary ());
				break;
			
			case 'tldsummaryowner' :
				$this->view->title = $this->translator->translate("Summary by Client");
				$this->view->description = $this->translator->translate("By this list you can see the summary of the domains bought per client.");
				$this->view->graph = "";
				$this->view->data = array ('records' => Domains::domains_per_customers (), 'pager' => true);
				break;
			
			case 'domainstasks' :
				$this->view->title = $this->translator->translate("List of all domain tasks (last 100 records)");
				$this->view->description = $this->translator->translate("By this list you can know all the tasks for each created domain.");
				$this->view->graph = "";
				$this->view->data = array ('records' => DomainsTasks::GetTask(100), 'delete' => array('controller' => 'reports', 'action' => 'deletetask'), 'pager' => true);
				break;
			
			case 'servicesummary' :
				
				// get all the recurring products and services as default
				$groups = ProductsAttributesGroups::getList(null, true);
				if(!empty($groups)){
					$groups = array_keys($groups);
				}
				$groups = array('3', '9');
				
				$fields = "detail_id,
							o.order_id as orderid,
							c.customer_id as customer_id,  
							oid.relationship_id as relationship_id,
							DATE_FORMAT(oi.date_end, '%d/%m/%Y') as expiringdate,
							d.autorenew as autorenew,
							CONCAT(c.firstname, ' ', c.lastname, ' ', c.company) as customer,
							oi.description as description,
							CONCAT(d.domain, '.', d.tld) as domain,  
							oi.cost as cost, 
							oi.price as price";
				
				$this->view->title = $this->translator->translate("List of the Recurring Services");
				$this->view->description = $this->translator->translate("By this list you can see the summary of the services bought per client.");
				$this->view->graph = "";
				$this->view->data = array ('records' => OrdersItems::getAllRecurringServices ( $fields, $groups ), 'pager' => true);
				break;
			
			case 'ticketsummay' :
				$this->view->title = $this->translator->translate("Tickets Summary");
				$this->view->description = $this->translator->translate("List of the last help requests.");
				$this->view->graph = "";
				$this->view->data = array ('records' => Tickets::Last(), 'actions' => array ('/admin/tickets/edit/id/' => 'show' ), 'pager' => true );
				break;
			
			case 'domainsexpiration' :
				$this->view->title = $this->translator->translate("Domain Expiration List");
				$this->view->description = $this->translator->translate("This view helps you to check which are all the domain next to the expiration.");
				$this->view->graph = "";
				$this->view->data = array ('records' => Domains::getExpiringDomains(), 'actions' => array ('/admin/domains/edit/id/' => 'show' ), 'pager' => true );
				break;
			
			case 'servicesexpiration' :
				$this->view->title = $this->translator->translate("Service Expiration List");
				$this->view->description = $this->translator->translate("This view helps you to check which are all the services next to the expiration.");
				$this->view->graph = "";
				$this->view->data = array ('records' => Products::getExpiringProducts(), 'actions' => array ('/admin/services/edit/id/' => 'show' ), 'pager' => true );
				break;
			
			default :
				$this->_helper->redirector ( 'show', 'reports', 'admin', array ('type'=>'profitofyear', 'q' => date('Y') ) );
				break;
		}
	}
		
	/**
	 * deletetaskAction
	 * delete a task action
	 * @return null
	 */
	public function deletetaskAction() {
		$id = $this->getRequest ()->getParam('id');
		
		if (is_numeric($id))
			DomainsTasks::DeleteTask($id);
			$this->_helper->redirector ( 'show', 'reports', 'admin', array ('type' => 'domainstasks', 'mex' => 'Domain task has been deleted.', 'status' => 'success' ) );
			
		$this->_helper->redirector ( 'show', 'reports', 'admin', array ('type' => 'domainstasks', 'mex' => 'Domain task has not been deleted.', 'status' => 'error' ) );
	}
}