<?php

class Utils{
	
	/* ===============================================
		Utils function, clean and test
	 =============================================== */

	public function flattenArray(array $array){
	
		$ret_array = array();
		  
		foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value)
		{
		   $ret_array[] = $value;
		}
		  
	    return $ret_array;
	}
	
	public function groupArray($array){
	
		return call_user_func_array('array_merge', $array);

	}
	
	// Test if the string is the same
	public function percent($category,$string){
	
		similar_text($category, $string , $percent);
				
		if( $percent >= 90)
		{	
			return true;
		}		
		
		return false;
	}
	
	// clean almost identical category string 
	public function cleanString($string ,$db = NULL){
		
		// remove *
		$string = str_replace('*', '', $string);
		
		if($db)
		{
			$string = str_replace('(en ', '(', $string);
		}
		// trim string	
		$string = trim($string);

		return $string;
	}
	
	public function objectToArray($d) {
	
        if (is_object($d)) 
        {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
        if (is_array($d)) 
        {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(array( __CLASS__, 'objectToArray' ), $d);
        }
        else 
        {
            // Return array
            return $d;
		}
		
    }
	
}