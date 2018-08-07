<?php

$uri = str_replace('/index.php', '', strtolower($_SERVER['SCRIPT_NAME']));

$uri = str_replace($uri, '', strtolower($_SERVER['REQUEST_URI']));

$a_uri = explode('/', $uri);

//Check if language is empty
if(empty($a_uri[1])){
	$a_uri[1] = Kohana::$config->load('appconf.language_abbr');

	$a_uri = array_filter($a_uri);
	$value = implode('/', $a_uri);
	
	header('Location: ' . URL::base() . $value);   
	die();
}

/**
 * Load language conf
 */
$lang 			= Kohana::$config->load('appconf.lang_uri_abbr');
$default_lang 	= Kohana::$config->load('appconf.language_abbr');
$lang_ignore	= Kohana::$config->load('appconf.lang_ignore');
$lang_abr 		= implode('|', array_keys($lang));

if(!empty($lang_abr)){
	$lang_abr .= '|' . $lang_ignore;
}
			  
//Fetching current controller's special routes
//Manually fetching controller's name
$nomController = (!empty($a_uri[2])) ? $a_uri[2] : 'index';
$nomaction = (!empty($a_uri[3])) ? $a_uri[3] : 'index';

$a_route_controller = Kohana::find_file('config', 'controller' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, strtolower($nomController)), 'php');	
if(!empty($a_route_controller)){
	$a_route_controller = require_once($a_route_controller[0]);
}

//If any, listing all found route
if(array_key_exists('route', $a_route_controller) && !empty($a_route_controller)){
	if(array_key_exists($nomaction, $a_route_controller['route']) && !empty($a_route_controller['route'])){
		$values = $a_route_controller['route'][$nomaction];
		
		$a_uri = $values['uri'];
		$regex   = array_merge(array('lang' => "({$lang_abr})"), 
							  (array) $values['regex']);
		$default = array_merge(array('lang' => $default_lang),
                              (array) $values['defaults']);

		Route::set((string)$nomaction, (string)$a_uri, (array)$regex)->defaults($default);	
	}
}

/**
 * Set the route. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
 
Route::set('default', 
		   '((<lang>)(/)(<action>(/<id>)))', 
		   array('lang' => "({$lang_abr})"))
	->defaults(array('lang' => $default_lang,
					 'controller' => 'index',
					 'action'     => 'index'));
					 
/*Route::set('catch-all', 
		   '<uri>', 
		   array('uri' => '.+'))
	->defaults(array('controller' => 'error',
					 'action' => '404'));*/