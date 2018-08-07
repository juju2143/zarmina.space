<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class URL extends Kohana_URL {
	/**
	* href link generator
	*
	* Returns a href tag after adding the user language in it
	*
	*/
	public static function link_to($title, $url, $option=array()){
	 
		$option_str = '';
		$site_url 	= URL::base(TRUE);
		$lng = Request::$initial->param('lang');
	 
		foreach($option as $key => $value){
			$option_str .= "{$key}='{$value}' ";
		}
	 
		return "<a href='{$site_url}{$lng}/{$url}' {$option_str}>{$title}</a>";
	}
	 
	/**
	* redirect
	*
	* redirect to the given controller/myaction after adding the user language
	*
	*/
	public static function redirect($to = ''){
		$site_url 	= URL::base(TRUE);
	
		$lng = Request::$initial->param('lang');
	 
		header("Location: {$site_url}{$lng}/{$to}");
		die();
	}
	
	/**
	* Fetches an absolute site URL based on a URI segment and supplied language.
	*/
	public static function lang(){
		$lang = Request::$initial->param('lang');

		return URL::base(TRUE) . $lang . "/";
	}
	
	public static function user_lang($session_name){
		$lang = Lang::allowed_session_lang($session_name);

		return URL::base(TRUE) . $lang . "/";
	}
	
	public static function inject_user_lang($url){
		$session_name = Kohana::$config->load('authentication.session');
		
		$allowed_abbr = array_keys(Kohana::$config->load('appconf.lang_uri_abbr'));
		
		$url_parts = explode('/', $url);
		
		$lang_index = array_search($allowed_abbr, $url_parts, true);
		
		$url_parts[$lang_index] = static::user_lang($session_name);
		
		return implode('/', $url_parts);
	}
	
	public static function current_lang(){
		return Request::$initial->param('lang');
	}
	
	/**
	* Fetches an absolute site URL based on a URI segment and supplied language.
	*/
	public static function get_lang(){
		return Request::$initial->param('lang');
	}
	
	/**
	* Fetches the current URI.
	*
	* @param   boolean  include the query string
	* @return  string
	*/
	public static function current()
	{
		return URL::site(Request::$initial->uri(), TRUE);
	}

	/**
	 * Get the opposite language code from a specified language code.
	 *
	 * @param   string  language code from which we want to get the opposite language
	 * @return  string  opposite language code of parameter if specified,  
	 * 					opposite language code of current locale lang otherwise 
	 */
	public static function opp_lang($lang = FALSE){
		if (!$lang)
			$lang = Request::current()->param('lang');

		switch ($lang){
			case 'fr':
				$opp_lang = 'en';
				break;
			case 'en':
			default:
				$opp_lang = 'fr';
				break;
		}

		return $opp_lang;
	}
	
	public static function opp_site($lang = FALSE){
		if (!$lang)
			$lang = Request::current()->param('lang');

		switch ($lang){
			case 'fr':
				$opp_lang = 'en';
				break;
			case 'en':
			default:
				$opp_lang = 'fr';
				break;
		}
		
		$site_opp = URL::base(TRUE) . $opp_lang . "/";

		return $site_opp;
	}
	
	
	public static function opp_link($lang = FALSE){
		if (!$lang)
			$lang = Request::current()->param('lang');

		switch ($lang){
			case 'fr':
				$opp_lang = 'en';
				break;
			case 'en':
			default:
				$opp_lang = 'fr';
				break;
		}
		
		$link_opp = URL::base(TRUE) . str_replace($lang.'/', $opp_lang.'/', Request::$initial->uri);

		return $link_opp;
	}
	
	public static function now($online = false) {
		$pageURL = 'http';
	 	if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
			$pageURL .= "s";
		}
	 	
		$pageURL .= "://";
		
		if($online){
			$domain = Kohana::$config->load('appconf.domain');
			
			if(!is_null($domain)){
				$server_name = $domain;
				
				//Generate the URI from the language
				$a_url = explode('/', $_SERVER["REQUEST_URI"]);
				$is_after_lang = false;
				$uri = '';
				$a_lang = Kohana::$config->load('appconf.lang_uri_abbr');
		
				if(!is_null($a_lang)){
					foreach($a_url as $value){
						if(!empty($value)){
							if(array_key_exists($value, $a_lang)){
								$is_after_lang = true;
							}
							
							if($is_after_lang){
								$uri .= $value . '/';
							}
						}
					}
				}else{
					$uri = $_SERVER["REQUEST_URI"];
				}
			}else{
				$uri = $_SERVER["REQUEST_URI"];
				$server_name = $_SERVER["SERVER_NAME"];
			}
		}else{
			$uri = $_SERVER["REQUEST_URI"];
			$server_name = $_SERVER["SERVER_NAME"];
		}
		
		if ($_SERVER["SERVER_PORT"] != "80") {
	  		$pageURL .= $server_name . ":" . $_SERVER["SERVER_PORT"] . $uri;
	 	} else {
	  		$pageURL .= $server_name . $uri;
	 	}

		return $pageURL;
	}
	
	public static function convert_uri($str){
		$str = Helper_Str::striptags($str); 
		$str = Helper_Str::strip_accent($str);
		$str = htmlspecialchars($str);
		$str = URL::title($str);

		return $str;	
	}
		
	/**
	 * Encode variable to Base64 to allow it as a parameter
	 */
	public static function base64_encode($value) {
		return str_replace('=', '__eg__', base64_encode($value));
	}
	
	public static function base64_decode($value) {
		return base64_decode(str_replace('__eg__', '=', $value));
	}

	// sauce: https://wpsmith.net/2012/a-real-regex-for-urls-using-preg_match/
	public static function validate($url) {
		
		$validIpAddressRegex = '((([01]?[0-9]?[0-9]|2([0-4][0-9]|5[0-5]))\.){3}([01]?[0-9]?[0-9]|2([0-4][0-9]|5[0-5])))';
		$validHostnameRegex = '([a-z\d]([a-z\d\-]{0,61}[a-z\d])?(\.[a-z\d]([a-z\d\-]{0,61}[a-z\d])?)*)';
		// initial host regex : ([a-z0-9-.]*).([a-z]{2,3}) // Host or IP
		
		$regex  = '((https?|ftps?):\/\/)?'; // SCHEME
		$regex .= '([a-z0-9+!*(),;?&=$_.-]+(:[a-z0-9+!*(),;?&=$_.-]+)?@)?'; // User and Pass
		$regex .= '([a-z\d]([a-z\d\-]{0,61}[a-z\d])?(\.[a-z\d]([a-z\d\-]{0,61}[a-z\d])?)*)'; // Host
		$regex .= '(:[0-9]{2,5})?'; // Port
		$regex .= '(\/([a-z0-9+$_-].?)+)*\/?'; // Path
		$regex .= '(\?[a-z+&$_.-][a-z0-9;:@&%=+\/$_.-]*)?'; // GET Query
		$regex .= '(#[a-z_.-][a-z0-9+$_.-]*)?'; // Anchor
		
		if( preg_match('/^' . $regex . '$/i', $url)){
			return true;
		}
		return false;
	}
}
?>