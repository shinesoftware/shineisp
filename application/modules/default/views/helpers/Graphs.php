<?php
/**
 * Profile helper
 */
class Zend_View_Helper_Graphs extends Zend_View_Helper_Abstract {
	public $view;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	public function graphs() {
		return $this;
	}
	
	public function tldSummary() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$registry = Zend_Registry::getInstance ();
		$translation = $registry->Zend_Translate;
		
		if (!empty($NS->customer)) {
			$data = $NS->customer;
			$summary = Domains::getSummary ( $data ['customer_id'] );
			if (isset ( $summary [0] )) {
				foreach ( $summary as $item ) {
					$totals [] = $item ['total'];
					$tlds [] = $item ['tld'];
				}
				$totals = implode ( ",", $totals );
				$tlds = implode ( "|", $tlds );
				
				$this->view->uri = "http://chart.apis.google.com/chart?cht=bvs&chtt=" . $translation->translate ( 'Domains Summary' ) . "&chts=4d89f9,18&chs=380x300&chd=t:" . $totals . "&chxt=x&chxl=0:|" . strtoupper ( $tlds ) . "&chco=4d89f9,c6d9fd&chds=0," . ($summary [0] ['total'] + 100) . "&chm=N,000000,0,-1,13&chbh=a";
			}
		}
		return $this->view->render ( 'partials/graph.phtml' );
	}
	
	/*
     * tldSummaryPerMonth
     * Full list of tld expiring month by month
     */
	public function tldSummaryPerMonth() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$registry = Zend_Registry::getInstance ();
		$translation = $registry->Zend_Translate;
		$domains = array ();
		
		if (!empty($NS->customer)) {
			$this->view->uri = "";
			$data = $NS->customer;
			
			// Get the data information
			$all_domains = Domains::getSummaryPerMonth ( $data ['customer_id'] );
			$autorenew_domains = Domains::getAutorenewSummaryPerMonth ( $data ['customer_id'] );
			
			for($i = 1; $i <= 12; $i ++) {
				 
				$domains ['months'] [$i] = date('M', strtotime( date('Y/m/01/',strtotime("2012/12/1"))." $i month"));
				$domains ['autorenew'] [$i] = 0;
				$domains ['norenew'] [$i] = 0;
				
				foreach ( $autorenew_domains as $domain ) {
					if ($domain ['monthid'] == $i) {
						$domains ['autorenew'] [$i] = $domain ['total'];
					}
				}
				
				foreach ( $all_domains as $domain ) {
					if ($domain ['month_number'] == $i) {
						$domains ['norenew'] [$i] = $domain ['total'] - $domains ['autorenew'] [$i] ;
					}
				}
			
			}
			
			$norenew = implode ( ",", $domains ['norenew'] );
			$autorenew = implode ( ",", $domains ['autorenew'] );
			$months = implode ( "|", $domains ['months'] );
			$max = max ( $domains ['autorenew'] );
			
			$this->view->uri = "http://chart.apis.google.com/chart?cht=bvg
			&chdlp=t
			&chtt=" . $translation->translate ( 'Domain Summary per Month' ) . "
			&chdl=" . $translation->translate ( 'Autorenew' ) . "|" . $translation->translate ( 'No Autorenew' ) . "
			&chts=4d89f9,18
			&chs=550x300
			&chd=t:" . $autorenew . "|" . $norenew . "
			&chxt=x
			&chxl=0:|" . $months . "
			&chco=4d89f9,FF1F1F
			&chds=0," . $max . "
			&chm=N,000000,-1,,11|N,000000,0,,11
			&chbh=a";
		}
		return $this->view->render ( 'partials/graph.phtml' );
	}
	
	/*
     * cmp
     * Sorting function
     */
	private function cmp($a, $b) {
		if ($a ["total"] == $b ["total"]) {
			return 0;
		}
		return ($a ["total"] < $b ["total"]) ? - 1 : 1;
	}

}