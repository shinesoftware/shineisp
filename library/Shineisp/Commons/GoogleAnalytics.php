<?php

/**
 * 
 * Create the Google Analytics Conversion
 * 
 * <script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'GOOGLE_ANALYTICS_CODE']);
		_gaq.push(['_trackPageview']);
		_gaq.push(['_addTrans', 'AZ-00011', 'Main Website Store', '0.5000', '0.0000', '0.0000', 'Trieste', '', 'IT']);
		_gaq.push(['_addItem', 'AZ-00011', '0042', 'TEST', '', '0.5000', '1.0000']);
		_gaq.push(['_trackTrans']);
		
		(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
 * 
 * @author shinesoftware
 *
 */

class Shineisp_Commons_GoogleAnalytics {
	protected $accountId;
	protected $orderId;
	protected $pushItems;
	
	/**
	 * @return the $pushItems
	 */
	public function getPushItems() {
		return $this->pushItems;
	}

	/**
	 * @param field_type $pushItems
	 */
	public function setPushItems($pushItems) {
		$this->pushItems[] = $pushItems;
		return $this;
	}

	/**
	 * @return the $orderId
	 */
	public function getOrderId() {
		return $this->orderId;
	}

	/**
	 * @param field_type $orderId
	 */
	public function setOrderId($orderId) {
		$this->orderId = $orderId;
		return $this;
	}

	/**
	 * @return the $accountId
	 */
	public function getAccountId() {
		return $this->accountId;
	}

	/**
	 * @param field_type $accountId
	 */
	public function setAccountId($accountId) {
		$this->accountId = $accountId;
		return $this;
	}

	/**
	 * create a push item
	 */
	public static function addPush(array $value){
		self::setPushItems($value);
	}
	
	/**
	 * create order transaction item
	 */
	private static function _addTrans($orderId){
		
		// Start the transaction
		self::addPush(array("_addTrans"));
		
		// Create the transaction item
		self::addPush(array('_addTrans', 'AZ-00011', 'Main Website Store', '0.5000', '0.0000', '0.0000', 'Trieste', '', 'IT'));
		
		// Add the items in the transaction
		self::_addItems($orderId);
		
		// Send the transaction to the Google Analytics
		self::addPush(array("_trackTrans"));
		
		return $this;
	}
	
	/**
	 * create order items
	 */
	private static function _addItems($orderId){
		self::addPush(array('_addItem', 'AZ-00011', '0042', 'TEST', '', '0.5000', '1.0000'));
	}
	
	/**
	 * create order items lists
	 */
	public static function addOrder($orderId){
		
		self::_addTrans($orderId);
		
		return $this;
	}
	
	/**
	 * create the Javascript code
	 */
	public protected function build(){
		
		self::addPush(array("_setAccount", self::getAccountId()));
		self::addPush(array("_trackPageview"));
		
		$items = self::getPushItems();
		
		$script[] = "<script type=\"text/javascript\">";
		$script[] = "var _gaq = _gaq || [];";
		$script[] = "(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\nga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\nvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n})();";
		$script[] = implode("\n", $items);
		$script[] = "</script>";
		
		return implode("\n", $script);
	}
	
}