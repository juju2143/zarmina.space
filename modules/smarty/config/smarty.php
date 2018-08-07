<?php
	return array(
		'version' 				=> '3', 		//either version 2.6 or 3
		'integration'           => true,        // Enable/Disable Smarty integration
		'index_template'        => 'index.tpl',
		'auto_render'           => true,
		'templates_ext'         => 'tpl',
		'default_templates'     => 'debug.tpl',
		'cache' 			    => false,
		'debugging' 			=> false,
		'debugging_ctrl'        => false,
		'security' 				=> false,
		'force_compile' 		=> false,
		'error_reporting'		=> null,
		'php_handling' 			=> 0, //a number between 0 and 3, chechk smarty for SMARTY_PHP_* constants

		'template_dir' 			=> APPPATH.'views'.DIRECTORY_SEPARATOR,
		'smarty_dir' 			=> //MODPATH.'smarty'.DIRECTORY_SEPARATOR,
									APPPATH.'vendor'.DIRECTORY_SEPARATOR.
											'smarty'.DIRECTORY_SEPARATOR.
											'smarty'.DIRECTORY_SEPARATOR.
											'libs'.DIRECTORY_SEPARATOR,
		'template_smarty_dir'	=> MODPATH.'smarty'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR,
		'compile_dir' 			=> APPPATH.'cache'.DIRECTORY_SEPARATOR,
		'cache_dir' 			=> APPPATH.'cache'.DIRECTORY_SEPARATOR,
		'config_dir' 			=> MODPATH.'smarty'.DIRECTORY_SEPARATOR.'template_config'.DIRECTORY_SEPARATOR,
		'plugin_dir' 			=> array(APPPATH.'vendor'.DIRECTORY_SEPARATOR.
													'smarty'.DIRECTORY_SEPARATOR.
													'smarty'.DIRECTORY_SEPARATOR.
													'libs'.DIRECTORY_SEPARATOR.
													'plugins'.DIRECTORY_SEPARATOR),
		'abs_media_dir'      	=> ROOTPATH . "medias".DIRECTORY_SEPARATOR,
		
		'include_before' 		=> array(),
		'include_after' 		=> array(),
		
		'left_delimiter' 		=> '<!--{',
		'right_delimiter' 		=> '}-->',
		
		'secure_dirs'           => array(APPPATH.'views',
										 MODPATH.'smarty/views'),
		
		'if_funcs'              => array('array',  
										 'list',     
										 'trim',       
										 'isset', 
										 'empty',
										 'sizeof', 
										 'in_array', 
										 'is_array',   
										 'true',  
										 'false',
										 'null',   
										 'reset',    
										 'array_keys', 
										 'end',   
										 'count'),
		
		'modifier_funcs'        => array ('sprintf', 
										  'count'),
	
		'post_filters'          => array(),
		'output_filters'        => array(),
		'pre_filters'           => array(),
		'escape_exclude_list'   => array(),
		
		//'base' => array('header' => 'meta/header.tpl', 'footer' => 'meta/footer.tpl'),
	);
?>