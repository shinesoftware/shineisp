<?php

abstract class Shineisp_Banks_BNL_Igfs_BaseIgfsCgTran extends Shineisp_Banks_BNL_Igfs_BaseIgfsCg {

	public $shopID; // chiave messaggio

	public $tranID;

	function __construct() {
		parent::__construct();
	}

	protected function resetFields() {
		parent::resetFields();
		$this->shopID = NULL;

		$this->tranID = NULL;
	}

	protected function checkFields() {
		parent::checkFields();
		if ($this->shopID == NULL || "" == $this->shopID)
			Shineisp_Commons_Utilities::logs ("---> Missing shopID", 'bnl_igfs.log');
			return false;
	}

	protected function buildRequest() {
		$request = parent::buildRequest();
		$request = $this->replaceRequest($request, "{shopID}", $this->shopID);
		return $request;
	}

	protected function getServicePort() {
		return "PaymentTranGatewayPort";
	}

	protected function parseResponseMap($response) {
		parent::parseResponseMap($response);
		// Opzionale
		$this->tranID = $response["tranID"];
	}

}

?>
