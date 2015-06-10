<?php

class Shineisp_Banks_BNL_Igfs_CgConfirm extends Shineisp_Banks_BNL_Igfs_BaseIgfsCgTran {

	public $amount;
	public $refTranID;
	public $topUpID;
	public $splitTran;

	public $addInfo1;
	public $addInfo2;
	public $addInfo3;
	public $addInfo4;
	public $addInfo5;
	public $pendingAmount;

	function __construct() {
		parent::__construct();
	}

	protected function resetFields() {
		parent::resetFields();
		$this->amount = NULL;
		$this->refTranID = NULL;
		$this->topUpID = NULL;
		$this->splitTran = NULL;

		$this->addInfo1 = NULL;
		$this->addInfo2 = NULL;
		$this->addInfo3 = NULL;
		$this->addInfo4 = NULL;
		$this->addInfo5 = NULL;
		$this->pendingAmount = NULL;
	}

	protected function checkFields() {
		parent::checkFields();
		if ($this->amount == NULL)
			Shineisp_Commons_Utilities::logs ("---> Missing amount", 'bnl_igfs.log');
			return false;

		if ($this->refTranID == NULL)
		if ($this->topUpID == NULL)
			Shineisp_Commons_Utilities::logs ("---> Missing refTranID", 'bnl_igfs.log');
			return false;
			
	}

	protected function buildRequest() {
		$request = parent::buildRequest();
		$request = $this->replaceRequest($request, "{amount}", $this->amount);

		if ($this->refTranID != NULL)
			$request = $this->replaceRequest($request, "{refTranID}", "<refTranID><![CDATA[" . $this->refTranID . "]]></refTranID>");
		else
			$request = $this->replaceRequest($request, "{refTranID}", "");
		if ($this->topUpID != NULL)
			$request = $this->replaceRequest($request, "{topUpID}", "<topUpID><![CDATA[" . $this->topUpID . "]]></topUpID>");
		else
			$request = $this->replaceRequest($request, "{topUpID}", "");

		if ($this->splitTran != NULL)
			$request = $this->replaceRequest($request, "{splitTran}", "<splitTran><![CDATA[" . $this->splitTran . "]]></splitTran>");
		else
			$request = $this->replaceRequest($request, "{splitTran}", "");

		return $request;
	}

	protected function setRequestSignature($request) {
		// signature dove il buffer e' cosi composto APIVERSION|TID|SHOPID|AMOUNT|REFORDERID
		$fields = array(
				$this->getVersion(), // APIVERSION
				$this->tid, // TID
				$this->shopID, // SHOPID
				$this->amount, // AMOUNT
				$this->refTranID, // REFORDERID
				$this->topUpID);
		$signature = $this->getSignature($this->kSig, // KSIGN
				$fields); 
		$request = $this->replaceRequest($request, "{signature}", $signature);
		return $request;
	}

	protected function getSoapResponseName() {
		return "ns1:ConfirmResponse";
	}

	protected function parseResponseMap($response) {
		parent::parseResponseMap($response);
		// Opzionale
		$this->addInfo1 = !empty($response["addInfo1"]) ? $response["addInfo1"] : null;
		// Opzionale
		$this->addInfo2 = !empty($response["addInfo2"]) ? $response["addInfo2"] : null;
		// Opzionale
		$this->addInfo3 = !empty($response["addInfo3"]) ? $response["addInfo3"] : null;
		// Opzionale
		$this->addInfo4 = !empty($response["addInfo4"]) ? $response["addInfo4"] : null;
		// Opzionale
		$this->addInfo5 = !empty($response["addInfo5"]) ? $response["addInfo5"] : null;
		// Opzionale
		$this->pendingAmount = !empty($response["pendingAmount"]) ? $response["pendingAmount"] : null;
	}

	protected function getResponseSignature($response) {
		$fields = array(
				$response["tid"], // TID
				$response["shopID"], // SHOPID
				$response["rc"], // RC
				$response["errorDesc"],// ERRORDESC
				$response["tranID"], // ORDERID
				$response["date"], // TRANDATE
				$response["addInfo1"], // UDF1
				$response["addInfo2"], // UDF2
				$response["addInfo3"], // UDF3
				$response["addInfo4"], // UDF4
				$response["addInfo5"]);// UDF5
		// signature dove il buffer e' cosi composto TID|SHOPID|RC|ERRORDESC|ORDERID|DATE|UDF1|UDF2|UDF3|UDF4|UDF5
		return $this->getSignature($this->kSig, // KSIGN
				$fields); 
	}
	
	protected function getFileName() {
		return "IgfsCgConfirm.request";
	}

}

?>
