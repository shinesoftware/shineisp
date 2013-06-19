<?php
class Shineisp_Commons_TimeSince {
	
	/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
	public static function time_since($original) {
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$original = strtotime($original);
		
	    // array of time period chunks
	    $chunks = array(
	        array(60 * 60 * 24 * 365 , $translator->translate('years')),
	        array(60 * 60 * 24 * 30 , $translator->translate('months')),
	        array(60 * 60 * 24 * 7, $translator->translate('weeks')),
	        array(60 * 60 * 24 , $translator->translate('days')),
	        array(60 * 60 , $translator->translate('hours')),
	        array(60 , $translator->translate('minutes')),
	    );
	    
	    $today = time(); /* Current unix time  */
	    $since = $today - $original;
	    
	    // $j saves performing the count function each time around the loop
	    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
	        
	        $seconds = $chunks[$i][0];
	        $name = $chunks[$i][1];
	        
	        // finding the biggest chunk (if the chunk fits, break)
	        if (($count = floor($since / $seconds)) != 0) {
	            // DEBUG print "<!-- It's $name -->\n";
	            break;
	        }
	    }
	    
	    $print = ($count == 1) ? '1 '.$name : "$count {$name}";
	    
	    if ($i + 1 < $j) {
	        // now getting the second item
	        $seconds2 = $chunks[$i + 1][0];
	        $name2 = $chunks[$i + 1][1];
	        
	        // add second item if it's greater than 0
	        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
	            $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}";
	        }
	    }
	    return $print;
	}
}