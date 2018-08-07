<?php
return array(
		'url_medias'     		 => URL::base() . "medias/",
		'path_styles'    		 => "styles/",
		'path_scripts'   		 => "scripts/",

		'language_enabled'		 => true,
		'language' 		 		 => 'francais',
		'language_abbr'  		 => 'fr',
		'lang_uri_abbr'  		 => array("fr" => "français", 
										  "en" => "english"),
		'lang_ignore' 	 		 => 'xx', //Note that it will look for a language named 'xx' in i18n. Therefore i18n:get('txt_sometext') will return 'txt_sometext'.
		'lang_desc' 	 		 => array("en" => "English version", 
										  "fr" => "Version française"),

		'debug_mode' 			=> false,
);