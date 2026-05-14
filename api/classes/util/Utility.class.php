<?php


require_once(__DIR__.'/../dao/parent/DBConnector.class.php');
class Utility{

	private static function createConnection(){
		return (new DBConnector())->connect();
	}
	
	public static function fetchRequestData($request){
		$returnObject = null;
		if($request != null){
			$returnObject = new stdClass();
			foreach($request as $key => $value){
				if(gettype($value) === "array"){
					$returnObject->$key = $value;
				}else if(gettype($value) === "string"){
					 $returnObject->$key =  self::escapeChar($value);
				}
			}
		}
		return $returnObject;
	}
	
	public static function escapeChar($string){
		return mysqli_escape_string(self::createConnection(), trim($string));
	}
	
	public static function encryptQueryString($string){
		return urlencode(base64_encode($string));
	}

	public static function decryptQueryString($string){
		return base64_decode(urldecode($string));
	}
	
	public static function tokenGen($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('A', 'Z'));
		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}
	
	public static function dateFormater($format,$date){
		$newDateFormat = date($format, strtotime($date));
		return $newDateFormat; 
	}
	
	public static function checkSlug($slug){
	    $e = trim($slug);
	    $s = preg_replace("/ {2,}/", " ", $e);
	    $final = strtolower(str_replace(' ', '-', $s));
	    $slug = preg_replace('/[(-+&#$^%@.)]/', '-', $final);
	    return $slug;
	}
}
?>