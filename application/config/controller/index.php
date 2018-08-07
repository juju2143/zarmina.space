<?php
return array(
	'route' => array(
		'index' => array('uri' => '<lang>/<action>',
						 'regex'   => array(),
						 'defaults'=> array('controller'  => 'index',
										    'action'      => 'index')),
													 
		'phpinfo' => array('uri'     => '<lang>/<action>',
						   'regex'   => array(),
						   'defaults'=> array('controller'  => 'index',
											  'action'      => 'phpinfo')),
													 
		'chapitre' => array('uri'     => '<lang>/<action>/<id>',
							'regex'   => array(),
							'defaults'=> array('controller' => 'index',
												'action'    => 'chapitre')),

		'page' => array('uri'     => '<lang>/<action>/<id>',
							'regex'   => array(),
							'defaults'=> array('controller' => 'index',
												'action'    => 'page')),

		'blog' => array('uri'     => '<lang>/<action>(/<offset>)',
							'regex'   => array(),
							'defaults'=> array('controller' => 'index',
												'action'    => 'blog',
												'offset'	=> 0)),

		'post' => array('uri'     => '<lang>/<action>/<id>',
							'regex'   => array(),
							'defaults'=> array('controller' => 'index',
												'action'    => 'post')),
	),
	
	'hierarchy' => array(
		'index' => array(
			'controller' => 'index',
		    'action' => 'index',
		    'qty_params' => 0,
		    'title' => __('title_index_index'),
		    'subtitle' => '',
		    'description' => '',
		    'tag' => '',
		    'sitemap' => TRUE,
		    'recursive' => FALSE,
			'controller_parent' => NULL,
			'action_parent' => NULL,
			'params' => array()
		),

		'chapitre' => array(
			'controller' => 'index',
		    'action' => 'chapitre',
		    'qty_params' => 1,
		    'title' => '',
		    'subtitle' => '',
		    'description' => '',
		    'tag' => '',
		    'sitemap' => TRUE,
		    'recursive' => FALSE,
			'controller_parent' => 'index',
			'action_parent' => 'index',
			'params' => array('id')
		),
			 
		'phpinfo' => array(
			'controller' => 'index',
		    'action' => 'phpinfo',
		    'qty_params' => 0,
		    'title' => 'Phpinfo()',
		    'subtitle' => '',
		    'description' => '',
		    'tag' => '',
		    'sitemap' => FALSE,
		    'recursive' => FALSE,
			'controller_parent' => 'index',
			'action_parent' => 'index',
			'params' => array()
		),
		'page' => array(
			'controller' => 'index',
		    'action' => 'page',
		    'qty_params' => 0,
		    'title' => 'page',
		    'subtitle' => '',
		    'description' => '',
		    'tag' => '',
		    'sitemap' => FALSE,
		    'recursive' => FALSE,
			'controller_parent' => 'index',
			'action_parent' => 'index',
			'params' => array('id')
		),
		'blog' => array(
			'controller' => 'index',
		    'action' => 'blog',
		    'qty_params' => 0,
		    'title' => 'blog',
		    'subtitle' => '',
		    'description' => '',
		    'tag' => '',
		    'sitemap' => FALSE,
		    'recursive' => FALSE,
			'controller_parent' => 'index',
			'action_parent' => 'index',
			'params' => array('offset')
		),
		'post' => array(
			'controller' => 'index',
		    'action' => 'post',
		    'qty_params' => 0,
		    'title' => 'blog',
		    'subtitle' => '',
		    'description' => '',
		    'tag' => '',
		    'sitemap' => FALSE,
		    'recursive' => FALSE,
			'controller_parent' => 'index',
			'action_parent' => 'index',
			'params' => array('id')
		),
	),
	
	'Authorization' => array(
		'index' => array('dev' => array('read'), 
							'user_level_1' => array('read'), 
						 'user_level_2' => array('read'), 
						 'user_level_3' => array('read'), 
							'mod' => array('read'), 
						 'admin' => array('read')),
		'chapitre' => array('dev' => array('read'), 
							'user_level_1' => array('read'), 
						'user_level_2' => array('read'), 
						'user_level_3' => array('read'), 
							'mod' => array('read'), 
						'admin' => array('read')),
						
	   	'phpinfo' => array('dev' => array('read')),
		
		'page' => array('dev' => array('read'), 
							   'user_level_1' => array('read'), 
						   'user_level_2' => array('read'), 
						   'user_level_3' => array('read'), 
							   'mod' => array('read'), 
						   'admin' => array('read')),
	)
);
?>