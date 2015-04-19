<?php
class Shineisp_Commons_DomainChecker {
	
	protected $arFailedDomain = array ();
	protected $arAvailableDomain = array ();
	protected $arUnavailableDomain = array ();
	protected $requestTimeout = 30;
	
	
	public function checkDomainAvailability($domain) {
		$servers = array();
		
		// fix the domain name:
		$domain = strtolower ( trim ( $domain ) );
		$domain = preg_replace ( '/^http:\/\//i', '', $domain );
		$domain = preg_replace ( '/^www\./i', '', $domain );
		$domain = explode ( '/', $domain );
		$domain = trim ( $domain [0] );
		
		$whois = WhoisServers::getAll();
		if(!empty($whois)){
			foreach ($whois as $tld){
				$servers[$tld['tld']] = $tld['server'];
				$this->arWhoisServer[$tld['tld']] = array($tld['server'],$tld['response']);
			}
		}
		
		// split the TLD from domain name
		$_domain = explode ( '.', $domain );
		array_shift($_domain);
		$ext = implode( '.', $_domain );
		
		if (! isset ( $servers [$ext] )) {
			return "Error: No matching whois server found for $ext!" ;
		}
		
		$output = "";
		try{
			if(!empty($servers [$ext])){
				
				// connect to whois server:
				if ($conn = @fsockopen ( $servers [$ext], 43 )) {
					fwrite ( $conn, $domain . "\r\n" );
					while ( ! feof ( $conn ) ) {
						$output .= fgets ( $conn, 128 );
					}
					fclose ( $conn );
				} else {
					return 'Error: Could not connect to ' . $servers [$ext] . '!';
				}
				
				return $this->checkResult ( $domain, $ext, $output );
				
			}else{
				throw new Exception('Whois Server URL has been not found!'); 
			}
		}catch (Exception $e){
			echo $e->getMessage();
		}
		
		
	}
	
	private function checkResult($domain, $tld = '', $response, $status = 'success') {
		if ($status == 'error') {
			$msg = $response;
			$this->arFailedDomain [count ( $domainStatus )] = array ($domain, $response );
		} else {
			if (strpos ( $response, $this->arWhoisServer [$tld] [1] ) !== false) {
				return true;
			} else {
				return false;
			}
		}
		echo $msg;
	}
}
