<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Class: embeddedJavascript
 *  Information transfer from PHP to javascript
 */
class Embeddedjavascript{
	//Generation of an available PHP variables list to transmit to javascript
	public static function encode_var_to_js($array){
		
		$return_val = "";
		//Looping through the variable in order to encode them to be usable in javascript
		foreach ($array as $key => $value) {
			$return_val .= 'var ' . $key . ' = "' . $value . '";' . "\n";
		}
	
		return $return_val;
	}
	
	public static function get_new_date($date){
		$a_date = explode('-', $date);
		
		$return_val = "new Date(";
		
		$return_val .= intval($a_date[0]) . ", ";
		$return_val .= intval($a_date[1] - 1) . ", ";
		$return_val .= intval($a_date[2]);
		
		$return_val .= ")";
		
		return $return_val;
	}
	
	public static function get_array($array){
		$return_val = "[";
		
		//Looping through the variable in order to encode them to be usable in javascript
		foreach ($array as $key => $value) {
			if(is_numeric($value)){
				$return_val .= $value;
			}else{
				$return_val .= "'" . $value . "'";
			}
			$return_val .=  ',';
		}
		
		$return_val .= "]";
	
		return $return_val;
	} 
}
 
?>