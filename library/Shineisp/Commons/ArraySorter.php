<?php

/*
 * Shineisp_Commons_ArraySorter
* -------------------------------------------------------------
* Type:     class
* Name:     Shineisp_Commons_ArraySorter
* Purpose:  Class for the array sorter
* -------------------------------------------------------------
*/

class Shineisp_Commons_ArraySorter {
	public static function multisort ($array, $column, $method = SORT_ASC) {
		foreach ($array as $key => $row) {
			$newKey = '_'.$key;
			$array[$newKey] = $array[$key];
			unset($array[$key]);
		}

	  	foreach ($array as $key => $row) {
	    	$narray[$key] = $row[$column]; 
		}
		
	  	array_multisort($narray, $method, $array);

		foreach ($array as $key => $row) {
			preg_match('/^_(.*)$/', $key, $out);
			
			$newKey = $out[1];
			
			$array[$newKey] = $array[$key];
			unset($array[$key]);
		}

	  	return $array;
	}
}