<?php
class Shineisp_Commons_QueriesLogger extends Doctrine_EventListener {
	protected $queries;

    public function preStmtExecute(Doctrine_Event $event)
    {   
        $query  = $event->getQuery();
        $params = $event->getParams();

        //the below makes some naive assumptions about the queries being logged
        while (sizeof($params) > 0) {
            $param = array_shift($params); 

            if (!is_numeric($param)) {
                $param = sprintf("'%s'", $param);
            }   

            $query = substr_replace($query, $param, strpos($query, '?'), 1); 
        }   

        Shineisp_Commons_Utilities::logs ( $query, "queries.log" );
    }

    public function getQueries()
    {   
        return $this->queries;
    }
}