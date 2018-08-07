<?php

class Helper_User{
	public static function get_id_lang(){
		$session_name = Kohana::$config->load('authentication.session');
		
		if(isset($_SESSION[$session_name]) && isset($_SESSION[$session_name]['id_lang'])){
			return $_SESSION[$session_name]['id_lang'];
		}else{
			//English by default
			return 1;
		}
	}
}