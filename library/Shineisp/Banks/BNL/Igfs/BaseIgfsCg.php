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


abstract class Shineisp_Banks_BNL_Igfs_BaseIgfsCg {
	
	private static $version = "2.1.6";

	public $kSig; // chiave signature

	public $serverURL = NULL;
	public $serverURLs = NULL;
	public $cTimeout = 5000;
	public $timeout = 15000;
	
	public $proxy = NULL;

	public $tid = NULL;

	public $rc = NULL;
	public $error = NULL;
	public $errorDesc = NULL;

	protected $fields2Reset = false;
	protected $checkCert = true;
    
	function __construct() {
		$this->resetFields();
	}
   
	protected function resetFields() {
		$this->tid = NULL;
		$this->rc = NULL;
		$this->error = false;
		$this->errorDesc = NULL;
		$this->fields2Reset = false;
	}
	
	protected function checkFields() {
		if ($this->serverURL == NULL || "" == $this->serverURL)
			if ($this->serverURLs == NULL || sizeof($this->serverURLs) == 0)
				Shineisp_Commons_Utilities::logs ("---> Missing serverURL", 'bnl_igfs.log');
				return false;
		if ($this->kSig == NULL || "" == $this->kSig)
			Shineisp_Commons_Utilities::logs ("---> Missing kSig", 'bnl_igfs.log');
			return false;
		if ($this->tid == NULL || "" == $this->tid)
			Shineisp_Commons_Utilities::logs ("---> Missing tid", 'bnl_igfs.log');
			return false;
			
		return true;
	}

	/**
	 * Disable Certification Check on SSL HandShake
	 */
	public function disableCheckSSLCert() {
		$this->checkCert = false;
	}

	protected function getServerUrl($surl) {
		if (!Shineisp_Banks_BNL_Igfs_Utils::endsWith($surl, "/")) {
			$surl .= "/";
		}
		return $surl . $this->getServicePort();
	}
	
	abstract protected function getServicePort();
	
	public static function getVersion() {
		return Shineisp_Banks_BNL_Igfs_BaseIgfsCg::$version;
	}

	protected function replaceRequest($request, $find, $value) {
		if ($value == NULL)
			$value = "";
		return str_replace($find, $value, $request);
	}
	
	protected function buildRequest() {
		$request = $this->readFromJARFile($this->getFileName());
		
		$request = $this->replaceRequest($request, "{apiVersion}", $this->getVersion());
		
		$request = $this->replaceRequest($request, "{tid}", $this->tid);
		
		return $request;
	}
	
	abstract protected function getFileName();
	
	protected function readFromJARFile($filename) {
		// return file_get_contents($filename, true);
		$path = (pathinfo(__FILE__));
		$template = $path['dirname'] ."/". $filename;
		return file_get_contents($template);
	}
 
 	abstract protected function setRequestSignature($request);

	abstract protected function getResponseSignature($response);

	protected static $SOAP_ENVELOPE = "soap:Envelope";
	protected static $SOAP_BODY = "soap:Body";
	protected static $RESPONSE = "response";
	
	protected function parseResponse($response) {
			
			$response = str_replace("<soap:", "<", $response);
			$response = str_replace("</soap:", "</", $response);
			$dom = new SimpleXMLElement($response, LIBXML_NOERROR, false);
			if (count($dom)==0) {
				return;
			}

			$tmp = str_replace("<Body>", "", $dom->Body->asXML());
			$tmp = str_replace("</Body>", "", $tmp);
			$dom = new SimpleXMLElement($tmp, LIBXML_NOERROR, false);
			if (count($dom)==0) {
				return;
			}

			$root = Shineisp_Banks_BNL_Igfs_BaseIgfsCg::$RESPONSE;
			if (count($dom->$root)==0) {
				return;
			}

			$fields = Shineisp_Banks_BNL_Igfs_Utils::parseResponseFields($dom->$root);
			if (isset($fields)) {
				$fields[Shineisp_Banks_BNL_Igfs_BaseIgfsCg::$RESPONSE] = $response;
			}
			
			return $fields;
			
	}
	
	abstract protected function getSoapResponseName();

	protected function parseResponseMap($response) {
		$this->tid = $response["tid"];
		$this->rc = $response["rc"];
		if ($response["error"] == NULL) {
			$this->error = true;
		} else {
			$this->error = ("true" == $response["error"]);
		}
		$this->errorDesc = $response["errorDesc"];
	}

	protected function checkResponseSignature($response) {
		if ($response["signature"] == NULL)
			return false;
		$signature = $response["signature"];
		if ($signature != $this->getResponseSignature($response))
			return false;
		return true;
	}

	protected function process($url) {
		// Creiamo la richiesta
		$request = $this->buildRequest();
		
		if ($request == NULL) {
			Shineisp_Commons_Utilities::logs ("---> IGFS Request is null", 'bnl_igfs.log');
			return false;
		}
		// Impostiamo la signature
		$request = $this->setRequestSignature($request);
		
		// Inviamo la richiesta e leggiamo la risposta
		try {
		
			#Zend_Debug::dump($request);
			$response = $this->post($url, $request);
			#Zend_Debug::dump($response);

		} catch (Exception $e) {
			Shineisp_Commons_Utilities::logs ("---> Error: " . $e->getMessage(), 'bnl_igfs.log');
			return false;
		}	
			
		if ($response == NULL) {
			Shineisp_Commons_Utilities::logs ("---> IGFS Response is null", 'bnl_igfs.log');
			return false;
		}
		// Parsifichiamo l'XML
		return $this->parseResponse($response);
	}
	
	private function post($url, $request) {

		//open connection 
		$ch = curl_init();

		$httpHeader = array("Content-Type: text/xml; charset=\"utf-8\"");
        Shineisp_Commons_Utilities::logs ("---> IGFS POST to $url", 'bnl_igfs.log');
        
		//set the url, number of POST vars, POST data 
		curl_setopt($ch,CURLOPT_HTTPHEADER,$httpHeader); 		
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$this->cTimeout/1000);
		curl_setopt($ch,CURLOPT_TIMEOUT,$this->timeout/1000);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$request);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		if (!$this->proxy != NULL) {
			curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch,CURLOPT_PROXY, $this->proxy);		
		}
		if (!$this->checkCert) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		}

		//execute post 
		$result = curl_exec($ch);
		
		if (curl_errno($ch)) { 
			if (curl_errno($ch) == CURLE_OPERATION_TIMEOUTED) {
			    
                Shineisp_Commons_Utilities::logs ("------> Exception occurred " . curl_error($ch), 'bnl_igfs.log');
        
				throw new Exception(curl_error($ch));
			} else {
			    Shineisp_Commons_Utilities::logs ("------> Exception occurred " . curl_error($ch), 'bnl_igfs.log');
			    
				throw new Exception(curl_error($ch));
			}
        } else { 
			//close connection 
			curl_close($ch);	
        } 
		
		return $result;
	}

	public function execute() {
		try {
			$this->checkFields();

			if ($this->serverURL != null) {
				$mapResponse = $this->executeHttp($this->serverURL);
			} else {
				$i = 0;
				$sURL = $this->serverURLs[$i];
				$finished = false;
				while ( ! $finished ) {
					try {
						$mapResponse = $this->executeHttp($sURL);
						$finished = true;
					} catch (Shineisp_Banks_BNL_ConnectionException $e) {
						$i++;
						if ($i < sizeof($this->serverURLs) && $this->serverURLs[$i] != null) {
							$sURL = $this->serverURLs[$i];
						} else {
							throw $e;
						}
					}
				}
			}

			$this->parseResponseMap($mapResponse);
			$this->fields2Reset = true;
			if (!$this->error) {
				if (!$this->checkResponseSignature($mapResponse)) {
					Shineisp_Commons_Utilities::logs ("--------> Error: " . json_encode($mapResponse), 'bnl_igfs.log');
					return false;
				}
				return true;
			} else {
				Shineisp_Commons_Utilities::logs ("--------> Error: " . json_encode($mapResponse), 'bnl_igfs.log');
				return false;
			}
		} catch (Exception $e) {
			$this->resetFields();
			$this->fields2Reset = true;
			$this->error = true;
			$this->errorDesc = $e->getMessage();	
			Shineisp_Commons_Utilities::logs ("-------------> Error: " . $e->getMessage(), 'bnl_igfs.log');
			return false;
		}
	}

	private function executeHttp($url) {
		$requestMethod = "POST";
		// cTimeout;
		// timeout;
		$url = $this->getServerUrl($url);
		$contentType = $this->getContentType();

		try {
			$mapResponse = $this->process($url);
		} catch (Exception $e) {
			throw $e;
		}
		if ($mapResponse == NULL) {
			throw new Exception("Invalid IGFS Response");
		}

		return $mapResponse;
	}

	protected function getContentType() {
		return "text/xml; charset=\"utf-8\"";
	}

	protected function getSignature($key, $fields) {
		try {
			return Shineisp_Banks_BNL_Igfs_Utils::getSignature($key, $fields);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	protected function getUniqueBoundaryValue() {
		return Shineisp_Banks_BNL_Igfs_Utils::getUniqueBoundaryValue();
	}

}
?>
