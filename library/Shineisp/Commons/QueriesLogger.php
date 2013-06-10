<?php
class Shineisp_Commons_QueriesLogger extends Doctrine_EventListener {
	protected $queries;

    public function preStmtExecute(Doctrine_Event $event)
    {   
    	$breadcrumps = array();
        $query  = $event->getQuery();
        $params = $event->getParams();
        $callers = array_reverse(debug_backtrace(), true);
        $callers = array_slice($callers, 4, count($callers) -10);
        
        foreach ($callers as $caller){
        	$class = !empty($caller['class']) ? $caller['class'] : null;
        	$breadcrumps[] = $class . "->" . $caller['function'] ;
        }
        
        $strBreadcrump = "System: " . implode(" / ", $breadcrumps);
        
        //the below makes some naive assumptions about the queries being logged
        while (sizeof($params) > 0) {
            $param = array_shift($params); 

            if (!is_numeric($param)) {
                $param = sprintf("'%s'", $param);
            }   

            $query = substr_replace($query, $param, strpos($query, '?'), 1); 
        }   

        Shineisp_Commons_Utilities::log ( $query, "queries.log" );
        Shineisp_Commons_Utilities::log ( $strBreadcrump, "debug.log", Zend_Log::DEBUG);
		
		// Increase query counter
		$queryCount = Shineisp_Registry::isRegistered('querycount') ? Shineisp_Registry::get('querycount') : 0;
		$queryCount = $queryCount +1;
		
		Shineisp_Registry::set('querycount', $queryCount);
		
    }

    public function getQueries()
    {   
        return $this->queries;
    }
}