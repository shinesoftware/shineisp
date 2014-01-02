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
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
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
		$links = "";
		
		switch ($datatype) {
			case 'profitofyear' :
				if(is_numeric($param)){
					$links = $this->translator->_("Click one of these links to show the economic reports");
					$years = Invoices::getYears();
					
					foreach ($years as $year) {
						$links .= " <a href='/admin/reports/show/type/profitofyear/q/$year'>$year</a>";
					}
					
					$this->view->title = $this->translator->_("Estimated Revenue for %s", $param);
					
					if(!empty($years)){
						$this->view->description = $this->translator->_("Below is the economic summary of the %s.", $param) . " " . $links;
					}else{
						$this->view->description = $this->translator->_("Below is the economic summary of the %s.", $param);
					}
					$this->view->year = $param;
					
					$graph = new Shineisp_Commons_Morris();
					
					// Get the total of the revenues per year
					$graphdata = $graph->setType('Area')
										->setData(Orders::prepareGraphData(array($param), 'month', false))
										->setElement('graph')
										->setXkey('xdata')
										->setLabels(array($this->translator->translate('Net Revenue (Taxable Income less Costs)')))
										->setOptions(array('lineColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
										->plot();
					
					$this->view->placeholder ( "admin_endbody" )->append ($graphdata);
					
					Invoices::getSummaryGrid($this->_helper, $param);
					PurchaseInvoices::getSummaryGrid($this->_helper, $param);
					
				}else{
					$this->_helper->redirector ( 'show', 'reports', 'admin', array ('type'=>'profitofyear', 'q' => date('Y') ) );
				}
				break;
			
			case 'productsummary' :
				$years = Invoices::getYears();
					
				foreach ($years as $year) {
					$links .= " <a href='/admin/reports/show/type/productsummary/q/$year'>$year</a>";
				}
				
				if(!empty($years)){
					$this->view->description = $this->translator->_("In this list you can see the summary of the products sold. %s", $param) . " <br/> " . $links . " <a href='/admin/reports/show/type/productsummary/'>".$this->translator->translate('Show All')."</a> ";
				}else{
					$this->view->description = $this->translator->_("In this list you can see the summary of the products sold. %s", $param);
				}
				
				$this->view->title = $this->translator->translate("Products summary");
				$this->view->data = array ('records' => Products::getBestseller($param));
				break;
			
			case 'tldsummarypermonth' :
				$this->view->title = $this->translator->translate("Domain TLD monthly summary");
				$this->view->description = $this->translator->translate("In this list you can see the summary of the TLD per month.");
				
				$graph = new Shineisp_Commons_Morris();
				$data = Domains::prepareGraphDataperMonth();
				if(!empty($data)){
					// Get the total of the revenues per year
					$graphdata = $graph->setType('Bar')
												->setData($data)
												->setElement('graph')
												->setXkey('xdata')
												->setLabels(array_keys($data['tld']))
												->plot();
						
					$this->view->placeholder ( "admin_endbody" )->append ($graphdata);
				}
				$this->view->data = array ('records' => Domains::getSummaryPerMonth ());
				break;
			
			case 'domainstats' :
				$this->view->title = $this->translator->translate("Domain stats");
				$this->view->description = $this->translator->translate("This list shows all the costs and earnings of the domains sold grouped by tld.");

				$graph = new Shineisp_Commons_Morris();
				
				// Get the tlds domains per type
				$graphdata = $graph->setType('Donut')
										->setData(Domains::prepareGraphData ())
										->setElement('graph')
										->plot();
					
				$this->view->placeholder ( "admin_endbody" )->append ($graphdata);
				
				$this->view->data = array ('records' => Domains::getSummary ());
				break;
			
			case 'tldsummaryowner' :
				$this->view->title = $this->translator->translate("Summary per client");
				$this->view->description = $this->translator->translate("By this list you can see the summary of the domains bought per client.");
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
				
				$this->view->title = $this->translator->translate("List of the recurring services");
				$this->view->description = $this->translator->translate("By this list you can see the summary of the services bought per client.");
				$this->view->graph = "";
				$this->view->data = array ('records' => OrdersItems::getAllRecurringServices ( $fields, $groups ), 'pager' => true);
				break;
			
			case 'ticketsummary' :
				$this->view->title = $this->translator->translate("Ticket summary");
				$this->view->description = $this->translator->translate("List of the last help requests.");
				$this->view->graph = "";
				$this->view->data = array ('records' => Tickets::Last(), 'actions' => array ('/admin/tickets/edit/id/' => 'show' ), 'pager' => true );
				break;
			
			case 'domainsexpiration' :
				$this->view->title = $this->translator->translate("Expiration list of domains");
				$this->view->description = $this->translator->translate("This view helps you to check which are the domains next to expiration.");
				$this->view->graph = "";
				$this->view->data = array ('records' => Domains::getExpiringDomains(), 'actions' => array ('/admin/domains/edit/id/' => 'show' ), 'pager' => true );
				break;
			
			case 'servicesexpiration' :
				$this->view->title = $this->translator->translate("Expiration list of services");
				$this->view->description = $this->translator->translate("This view helps you to check which are the services next to expiration.");
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
			
		$this->_helper->redirector ( 'show', 'reports', 'admin', array ('type' => 'domainstasks', 'mex' => 'Domain task has not been deleted.', 'status' => 'danger' ) );
	}
}