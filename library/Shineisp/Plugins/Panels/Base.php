<?php
/**
 * 
 * @author Shine Software
 *
 */
class Shineisp_Plugins_Panels_Base implements Shineisp_Plugins_Interface {
	
	protected $isLive;
	protected $name;
	protected $path;
	protected $session;
	protected $actions = array();
	
	public $events;
	
	/**
	 * Events Registration
	 *
	 * (non-PHPdoc)
	 * @see Shineisp_Plugins_Interface::events()
	 */
	public function events()
	{
		return Shineisp_Registry::get('em');
	}
						
	/**
	 * @return the $session
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * @param field_type $session
	 */
	public function setSession($session) {
		$this->session = $session;
	}

	/**
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param field_type $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return the $isLive
	 */
	public function getIsLive() {
		return $this->isLive;
	}

	/**
	 * @param field_type $isLive
	 */
	public function setIsLive($isLive) {
		$this->isLive = $isLive;
	}
	/**
	 * @return the $actions
	 */
	public function getActions() {
		return $this->actions;
	}

	/**
	 * @param field_type $actions
	 */
	public function setActions($actions) {
		$this->actions = $actions;
	}

	/**
	 * create a readable username
	 *
	 * @param array $customer
	 * @return multitype:string NULL
	 */
	public static function generateUsernames($customer) {
		$arrUsernames = array();
	
		if ( isset($customer ['company']) && !empty($customer ['company']) && !empty($customer ['vat']) && !empty($customer ['vat']) ) {
			// Microsoft Corp => microsoftcorp
			$arrUsernames[] = strtolower(preg_replace("#[^a-zA-Z0-9]*#", "", $customer ['company']));
		}
	
		// Jon Doe => jdoe
		$arrUsernames[]  = strtolower(preg_replace("#[^a-zA-Z0-9]*#", "", substr($customer ['firstname'], 0, 1).$customer ['lastname']));
	
		// Jon Doe => doej
		$arrUsernames[] = strtolower(preg_replace("#[^a-zA-Z0-9]*#", "", $customer['lastname'].substr($customer ['firstname'], 0, 1)));
	
		// Jon Doe => jond
		$arrUsernames[] = strtolower(preg_replace("#[^a-zA-Z0-9]*#", "", $customer['firstname'].substr($customer ['lastname'], 0, 1)));
	
		// Jon Doe => djon
		$arrUsernames[] = strtolower(preg_replace("#[^a-zA-Z0-9]*#", "", substr($customer ['lastname'], 0, 1).$customer ['firstname']));
	
		// fallback to each generated username followed by customer_id
		foreach ( $arrUsernames as $tmpUser ) {
			$arrUsernames[] = $tmpUser.$customer ['customer_id'];
		}
		// fallback to each generated username followed by timestamp
		foreach ( $arrUsernames as $tmpUser ) {
			$arrUsernames[] = $tmpUser.time();
		}
	
		return $arrUsernames;
	}
	
	
	/**
	 * Match all the product attribute fields and IspConfig fields
	 *
	 *
	 * Match all the system product attribute from ShineISP
	 * and the IspConfig fields set in the configuration file
	 * located in the /library/Shineisp/Plugins/Panels/IspConfig/config.xml
	 *
	 * @param array $attributes --> ShineISP System Product Attribute
	 * @param array $record 	--> IspConfig Client Record
	 * @return ArrayObject
	 */
	private function matchFieldsValues(array $attributes, array $record = array()) {
		$fields = array ();
	
		// Loop of system product attributes
		foreach ( $attributes as $attribute => $value ) {
			// Get the saved system attribute
			$sysAttribute = ProductsAttributes::getAttributebyCode($attribute);
				
			if(!empty($sysAttribute[0]['system_var'])){
				$sysVariable = $sysAttribute[0]['system_var'];
				// Get the system product attribute
				$modAttribute = Panels::getXmlFieldbyAttribute ( "IspConfig", $sysVariable );
	
				if(!empty($modAttribute ['field'])){
					// Sum the old resource value with the new ones
					if(!empty($record[$modAttribute ['field']])){
	
						/**
						 * Now we have to sum the resource previously added
						 * in the client IspConfig profile with the new one
						 */
						if($modAttribute ['type'] == "integer"){
							if($record[$modAttribute ['field']] == "-1"){
								$value = "-1";
							}else{
								$value += $record[$modAttribute ['field']];
							}
						}
					}
	
					if (! empty ( $value )) {
						$fields [$modAttribute ['field']] = $value;
					} else {
						$fields [$modAttribute ['field']] = $modAttribute ['default'];
					}
				}
			}
		}
	
		return $fields;
	}
	
	/**
	 * 
	 * Send the email profile to the user
	 */
	public function sendMail($task){
		$ISP = ISP::getByCustomerId($task['customer_id']);
		
		if ( !$ISP || !isset($ISP['isp_id']) || !is_numeric($ISP['isp_id']) ) {
			// ISP not found, can't send mail		
			return false;
		}
		
		// Get the service details
		$service = OrdersItems::getAllInfo($task['orderitem_id']);
		
		// If the setup has been written by the task action then ...
		if(!empty($service['setup'])){
			$setup = json_decode($service['setup'], true);
				
			// Get the service/product name
			$productname  = !empty($service['Products']['ProductsData'][0]['name']) ? $service['Products']['ProductsData'][0]['name'] : "";
			$welcome_mail = (!empty($service['Products']['welcome_mail_id']) && intval($service['Products']['welcome_mail_id']) > 0) ? intval($service['Products']['welcome_mail_id']) : 'new_hosting'; // new_hosting = fallback to old method if no template is set

			// Check if the customer is present in the service and if there is a welcome_mail set for the bought product 
			if( !empty($service['Orders']['Customers']) ){
				
				// Getting the customer
				$customer = $service['Orders']['Customers'];
				
				$strSetup = "";
				foreach ($setup as $section => $details) {
					$strSetup .= strtoupper($section) . "\n===============\n";
					foreach ($details as $label => $detail){
						$strSetup .= "$label: " . $detail . "\n"; 
					}
					$strSetup .= "\n";
				}
				
				Shineisp_Commons_Utilities::sendEmailTemplate($ISP ['email'], $welcome_mail, array(
					'setup'        => $strSetup
				   ,'fullname'     => $customer ['fullname']
                   ,'hostingplan'  => $productname
				   ,'controlpanel' => $ISP ['website'].":8080"
				   ,'signature'    => $ISP ['company']
				), null, null, null, $ISP, $customer['language_id']);
				
									
				
			}
		}
	
	}
	
}