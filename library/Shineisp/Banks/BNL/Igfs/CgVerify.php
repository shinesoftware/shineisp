<?php


class Shineisp_Banks_BNL_Igfs_CgVerify extends Shineisp_Banks_BNL_Igfs_BaseIgfsCgInit {

	public $paymentID;

	public $tranID;
	public $authCode;
	public $enrStatus;
	public $authStatus;
	public $brand;
	public $maskedPan;
	public $addInfo1;
	public $addInfo2;
	public $addInfo3;
	public $addInfo4;
	public $addInfo5;
	public $payInstrToken;
	public $expireMonth;
	public $expireYear;
	public $level3Info;
	public $additionalInfo;

	function __construct() {
		parent::__construct();
	}

	protected function resetFields() {
		parent::resetFields();
		$this->paymentID = NULL;

		$this->tranID = NULL;
		$this->authCode = NULL;
		$this->enrStatus = NULL;
		$this->authStatus = NULL;
		$this->brand = NULL;
		$this->maskedPan = NULL;
		$this->addInfo1 = NULL;
		$this->addInfo2 = NULL;
		$this->addInfo3 = NULL;
		$this->addInfo4 = NULL;
		$this->addInfo5 = NULL;
		$this->payInstrToken = NULL;
		$this->expireMonth = NULL;
		$this->expireYear = NULL;
		$this->level3Info = NULL;
		$this->additionalInfo = NULL;
	}


	protected function checkFields() {
		parent::checkFields();
		if ($this->paymentID == NULL || "" == $this->paymentID)
			Shineisp_Commons_Utilities::logs ("---> Missing paymentID", 'bnl_igfs.log');
			return false;
	}


	protected function buildRequest() {
		$request = parent::buildRequest();
		$request = $this->replaceRequest($request, "{paymentID}", $this->paymentID);
		return $request;
	}

	protected function setRequestSignature($request) {
		$fields = array(
				$this->getVersion(), // APIVERSION
				$this->tid, // TID
				$this->shopID, // SHOPID
				$this->paymentID); // PAYMENTID
		// signature dove il buffer e' cosi composto APIVERSION|TID|SHOPID|PAYMENTID
		$signature = $this->getSignature($this->kSig, // KSIGN
				$fields); 
		$request = $this->replaceRequest($request, "{signature}", $signature);
		return $request;
	}

	protected function getSoapResponseName() {
		return "ns1:VerifyResponse";
	}

	protected function parseResponseMap($response) {
		parent::parseResponseMap($response);
		// Opzionale
		$this->tranID = !empty($response["tranID"]) ? $response["tranID"] : null;
		// Opzionale
		$this->authCode = !empty($response["authCode"]) ? $response["authCode"] : null;
		// Opzionale
		$this->enrStatus = !empty($response["enrStatus"]) ? $response["enrStatus"] : null;
		// Opzionale
		$this->authStatus = !empty($response["authStatus"]) ? $response["authStatus"] : null;
		// Opzionale
		$this->brand = !empty($response["brand"]) ? $response["brand"] : null;
		// Opzionale
		$this->maskedPan = !empty($response["maskedPan"]) ? $response["maskedPan"] : null;
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
		$this->payInstrToken = !empty($response["payInstrToken"]) ? $response["payInstrToken"] : null;
		// Opzionale
		$this->expireMonth = !empty($response["expireMonth"]) ? $response["expireMonth"] : null;
		// Opzionale
		$this->expireYear = !empty($response["expireMonth"]) ? $response["expireMonth"] : null;
		// Opzionale
		if(!empty($response["level3Info"])){
			$this->level3Info = Shineisp_Banks_BNL_Level3Info::fromXml($response["level3Info"]);
		}
		// Opzionale
		$this->additionalInfo = !empty($response["additionalInfo"]) ? $response["additionalInfo"] : null;
	}

	protected function getResponseSignature($response) {
		$fields = array(
				$response["tid"], // TID
				$response["shopID"], // SHOPID
				$response["rc"], // RC
				$response["errorDesc"],// ERRORDESC
				$response["paymentID"],// PAYMENTID
				$response["tranID"],// ORDERID
				$response["authCode"],// AUTHCODE
				$response["enrStatus"],// ENRSTATUS
				$response["authStatus"]);// AUTHSTATUS
		// signature dove il buffer e' cosi composto TID|SHOPID|RC|ERRORDESC|PAYMENTID|REDIRECTURL
		return $this->getSignature($this->kSig, // KSIGN
				$fields); 
	}
	
	protected function getFileName() {
		return "IgfsCgVerify.request";
	}

}

?>
