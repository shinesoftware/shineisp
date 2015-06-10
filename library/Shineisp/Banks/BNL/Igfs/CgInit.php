<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Shine Software
 * @package    Epositivity
 * @copyright  Copyright (c) 2008 Shine Software (http://www.shinesoftware.com)
 */

class Shineisp_Banks_BNL_Igfs_CgInit extends Shineisp_Banks_BNL_Igfs_BaseIgfsCgInit {

	public $shopUserRef;
	public $trType = "AUTH";
	public $amount;
	public $currencyCode;
	public $langID = "IT";
	public $notifyURL;
	public $errorURL;
	public $addInfo1;
	public $addInfo2;
	public $addInfo3;
	public $addInfo4;
	public $addInfo5;
	public $payInstrToken;
	public $regenPayInstrToken;
	public $payInstrTokenExpire;
	public $payInstrTokenUsageLimit;
	public $level3Info;
	public $description;
	public $recurrent;
	public $freeText;
	public $topUpID;

	public $paymentID;
	public $redirectURL;

	function __construct() {
		parent::__construct();
	}

	protected function resetFields() {
		parent::resetFields();
		$this->shopUserRef = NULL;
		$this->trType = "AUTH";
		$this->amount = NULL;
		$this->currencyCode = NULL;
		$this->langID = "IT";
		$this->notifyURL = NULL;
		$this->errorURL = NULL;
		$this->addInfo1 = NULL;
		$this->addInfo2 = NULL;
		$this->addInfo3 = NULL;
		$this->addInfo4 = NULL;
		$this->addInfo5 = NULL;
		$this->payInstrToken = NULL;
		$this->regenPayInstrToken = NULL;
		$this->payInstrTokenExpire = NULL;
		$this->payInstrTokenUsageLimit = NULL;
		$this->level3Info = NULL;
		$this->description = NULL;
		$this->recurrent = NULL;
		$this->freeText = NULL;
		$this->topUpID = NULL;

		$this->paymentID = NULL;
		$this->redirectURL = NULL;
	}

	protected function checkFields() {
		parent::checkFields();
		if ($this->trType == NULL)
			throw new Exception("Missing trType");
		if ($this->trType != "TOKENIZE") {
			if ($this->amount == NULL)
				throw new Exception("Missing amount");
			if ($this->currencyCode == NULL)
				throw new Exception("Missing currencyCode");
		}
		if ($this->langID == NULL)
			throw new Exception("Missing langID");
		if ($this->notifyURL == NULL)
			throw new Exception("Missing notifyURL");
		if ($this->errorURL == NULL)
			throw new Exception("Missing errorURL");
		if ($this->payInstrToken != NULL) {
			// Se è stato impostato il payInstrToken verifico...
			if ($this->payInstrToken == "")
				throw new Exception("Missing payInstrToken");
		}
		if ($this->level3Info != NULL) {
			$i = 0;
			if ($this->level3Info->product != NULL) {
				foreach ($this->level3Info->product as $product) {
					if ($product->productCode == NULL)
						throw new Exception("Missing productCode[" . i . "]");
					if ($product->productDescription == NULL)
						throw new Exception("Missing productDescription[" . i . "]");
				}
				$i++;
			}
		}
			
	}

	protected function buildRequest() {
		$request = parent::buildRequest();
		if ($this->shopUserRef != NULL)
			$request = $this->replaceRequest($request, "{shopUserRef}", "<shopUserRef><![CDATA[" . $this->shopUserRef . "]]></shopUserRef>");
		else
			$request = $this->replaceRequest($request, "{shopUserRef}", "");

		$request = $this->replaceRequest($request, "{trType}", $this->trType);
		if ($this->amount != NULL)
			$request = $this->replaceRequest($request, "{amount}", "<amount><![CDATA[" . $this->amount . "]]></amount>");
		else
			$request = $this->replaceRequest($request, "{amount}", "");
		if ($this->currencyCode != NULL)
			$request = $this->replaceRequest($request, "{currencyCode}", "<currencyCode><![CDATA[" . $this->currencyCode . "]]></currencyCode>");
		else
			$request = $this->replaceRequest($request, "{currencyCode}", "");
		$request = $this->replaceRequest($request, "{langID}", $this->langID);
		$request = $this->replaceRequest($request, "{notifyURL}", $this->notifyURL);
		$request = $this->replaceRequest($request, "{errorURL}", $this->errorURL);
		
		if ($this->addInfo1 != NULL)
			$request = $this->replaceRequest($request, "{addInfo1}", "<addInfo1><![CDATA[" . $this->addInfo1 . "]]></addInfo1>");
		else
			$request = $this->replaceRequest($request, "{addInfo1}", "");
		if ($this->addInfo2 != NULL)
			$request = $this->replaceRequest($request, "{addInfo2}", "<addInfo2><![CDATA[" . $this->addInfo2 . "]]></addInfo2>");
		else
			$request = $this->replaceRequest($request, "{addInfo2}", "");
		if ($this->addInfo3 != NULL)
			$request = $this->replaceRequest($request, "{addInfo3}", "<addInfo3><![CDATA[" . $this->addInfo3 . "]]></addInfo3>");
		else
			$request = $this->replaceRequest($request, "{addInfo3}", "");
		if ($this->addInfo4 != NULL)
			$request = $this->replaceRequest($request, "{addInfo4}", "<addInfo4><![CDATA[" . $this->addInfo4 . "]]></addInfo4>");
		else
			$request = $this->replaceRequest($request, "{addInfo4}", "");
		if ($this->addInfo5 != NULL)
			$request = $this->replaceRequest($request, "{addInfo5}", "<addInfo5><![CDATA[" . $this->addInfo5 . "]]></addInfo5>");
		else
			$request = $this->replaceRequest($request, "{addInfo5}", "");
		
		if ($this->payInstrToken != NULL)
			$request = $this->replaceRequest($request, "{payInstrToken}", "<payInstrToken><![CDATA[" . $this->payInstrToken . "]]></payInstrToken>");
		else
			$request = $this->replaceRequest($request, "{payInstrToken}", "");
		if ($this->regenPayInstrToken != NULL)
			$request = $this->replaceRequest($request, "{regenPayInstrToken}", "<regenPayInstrToken><![CDATA[" . $this->regenPayInstrToken . "]]></regenPayInstrToken>");
		else
			$request = $this->replaceRequest($request, "{regenPayInstrToken}", "");
		if ($this->payInstrTokenExpire != NULL)
			$request = $this->replaceRequest($request, "{payInstrTokenExpire}", "<payInstrTokenExpire><![CDATA[" . Igfs_Utils::formatXMLGregorianCalendar($this->payInstrTokenExpire) . "]]></payInstrTokenExpire>");
		else
			$request = $this->replaceRequest($request, "{payInstrTokenExpire}", "");
		if ($this->payInstrTokenUsageLimit != NULL)
			$request = $this->replaceRequest($request, "{payInstrTokenUsageLimit}", "<payInstrTokenUsageLimit><![CDATA[" . $this->payInstrTokenUsageLimit . "]]></payInstrTokenUsageLimit>");
		else
			$request = $this->replaceRequest($request, "{payInstrTokenUsageLimit}", "");
		if ($this->level3Info != NULL)
			$request = $this->replaceRequest($request, "{level3Info}", $this->level3Info->toXml());
		else
			$request = $this->replaceRequest($request, "{level3Info}", "");
		if ($this->description != NULL)
			$request = $this->replaceRequest($request, "{description}", "<description><![CDATA[" . $this->description . "]]></description>");
		else
			$request = $this->replaceRequest($request, "{description}", "");
		if ($this->recurrent != NULL)
			$request = $this->replaceRequest($request, "{recurrent}", "<recurrent><![CDATA[" . $this->recurrent . "]]></recurrent>");
		else
			$request = $this->replaceRequest($request, "{recurrent}", "");
		if ($this->freeText != NULL)
			$request = $this->replaceRequest($request, "{freeText}", "<freeText><![CDATA[" . $this->freeText . "]]></freeText>");
		else
			$request = $this->replaceRequest($request, "{freeText}", "");
		if ($this->topUpID != NULL)
			$request = $this->replaceRequest($request, "{topUpID}", "<topUpID><![CDATA[" . $this->topUpID . "]]></topUpID>");
		else
			$request = $this->replaceRequest($request, "{topUpID}", "");
		return $request;
	}

	protected function setRequestSignature($request) {
		// signature dove il buffer e' cosi composto APIVERSION|TID|SHOPID|SHOPUSERREF|TRTYPE|AMOUNT|CURRENCYCODE|LANGID|NOTIFYURL|ERRORURL
		$fields = array(
				$this->getVersion(), // APIVERSION
				$this->tid, // TID
				$this->shopID, // SHOPID
				$this->shopUserRef, // SHOPUSERREF
				$this->trType,// TRTYPE
				$this->amount, // AMOUNT
				$this->currencyCode, // CURRENCYCODE
				$this->langID, // LANGID
				$this->notifyURL, // NOTIFYURL
				$this->errorURL, // ERRORURL
				$this->addInfo1, // UDF1
				$this->addInfo2, // UDF2
				$this->addInfo3, // UDF3
				$this->addInfo4, // UDF4
				$this->addInfo5, // UDF5
				$this->payInstrToken, // PAYINSTRTOKEN
				$this->topUpID);
		$signature = $this->getSignature($this->kSig, // KSIGN
				$fields); 
		$request = $this->replaceRequest($request, "{signature}", $signature);
		return $request;
	}

	protected function getSoapResponseName() {
		return "ns1:InitResponse";
	}

	protected function parseResponseMap($response) {
		parent::parseResponseMap($response);
		
		// Opzionale
		if(!empty($response["paymentID"])){
    		$this->paymentID = $response["paymentID"];
    	}
    	
		// Opzionale
		if(!empty($response["redirectURL"])){
		    $this->redirectURL = $response["redirectURL"];
		}
	}

	protected function getResponseSignature($response) {
		$fields = array(
				$response["tid"], // TID
				$response["shopID"], // SHOPID
				$response["rc"], // RC
				$response["errorDesc"],// ERRORDESC
				$response["paymentID"], // PAYMENTID
				$response["redirectURL"]);// REDIRECTURL	
		// signature dove il buffer e' cosi composto TID|SHOPID|RC|ERRORDESC|PAYMENTID|REDIRECTURL
		return $this->getSignature($this->kSig, // KSIGN
				$fields); 
	}
	
	protected function getFileName() {
		return "IgfsCgInit.request";
	}

}

?>
