<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Abstract controller class for automatic templating with smarty.
 *
 * @package    Controller
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @licence    http://kohanaphp.com/licence.html
 */
abstract class Kohana_Controller_Smarty extends Medias{
	
	public $request = NULL;
	
	public $response = NULL;

	/**
	 * @var  string  page template
	 */
	public $template = null;

	/**
	 * @var  object Smarty object
	 */
	public $smarty = null;
	
	/**
	 * @var  object Medias object
	 */
	public $medias = null;
	
	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = true;

	/**
	 * @var  boolean  allow integration
	 **/
	 public $integration = true;

	function __construct($request, $response){	

	  	$configs = Kohana::$config->load('smarty');
	   
	    // Assign the request to the controller
	    $this->request = $request;
		
		//Assign the response to the controller
		$this->response = $response;

	    //On va chercher l'état de l'intégration
	    $this->integration = $configs->get('integration');

	   	// Check if we should use smarty or not
       	if ($this->integration) {
       		$this->smarty = new Smarty();
			
			$base = Kohana::$config->load('smarty')->get('base');
			
			$this->smarty->template_dir   = $this->getTemplatesPaths($configs->get('template_dir'));
			
			//On s'arrange pour donner priorité aux templates de l'application
			$this->smarty->template_dir = array_reverse($this->smarty->template_dir);

			$this->smarty->cache_dir 	  = $configs->get('cache_dir');
			$this->smarty->compile_dir 	  = $configs->get('compile_dir');
			$this->smarty->config_dir     = $configs->get('config_dir');
			$this->smarty->plugins_dir    = $configs->get('plugin_dir');
			$this->smarty->debugging_ctrl = $configs->get('debugging_ctrl');
			$this->smarty->debugging      = $configs->get('debugging');
			$this->smarty->caching        = $configs->get('caching');
			$this->smarty->force_compile  = $configs->get('force_compile');
		
			$this->smarty->left_delimiter = $configs->get('left_delimiter');
			$this->smarty->right_delimiter= $configs->get('right_delimiter');
		   
			//$this->smarty->security       = $configs->get('security');
	
			// set default dirs
			$this->smarty->debug_tpl = Kohana::$config->load('smarty.template_smarty_dir') . Kohana::$config->load('smarty.default_templates');
	
			// check if cache directory is exists
			$this->checkDirectory(Kohana::$config->load('smarty.cache_path'));
	
			// check if smarty_compile directory is exists
			$this->checkDirectory($this->smarty->compile_dir);
			
			// check if smarty_cache directory is exists
			$this->checkDirectory($this->smarty->cache_dir);
	
			/*if ($this->smarty->security) {
				$configSecureDirectories = $configs->get('secure_dirs');
	
				$safeTemplates           = Array($configs->get('global_templates_path'));

				$this->smarty->secure_dir                          = array_merge($configSecureDirectories, $safeTemplates);
				$this->smarty->security_settings['IF_FUNCS']       = $configs->get('if_funcs');
				$this->smarty->security_settings['MODIFIER_FUNCS'] = $configs->get('modifier_funcs');
			}*/
			
			// Autoload filters
			$this->smarty->autoload_filters = Array('pre'    => $configs->get('pre_filters'),
													'post'   => $configs->get('post_filters'),
													'output' => $configs->get('output_filters'));
	
			// Add all helpers to plugins_dir
			$helpers = glob(APPPATH . 'helpers/*', GLOB_ONLYDIR | GLOB_MARK);
	
			foreach ($helpers as $helper) {
				$this->smarty->plugins_dir[] = $helper;
			}
			
			//On envoie à Smarty l'état du débug
			$this->smarty->assign('debug', $this->smarty->debugging);
			
			//On envoit le chemin absolue du site à Smarty
			$this->smarty->assign('abs_media_dir', $configs->get('abs_media_dir'));
			
			//Si le débug est activé, on charge le CSS de l'interface de debug
			if($this->smarty->debugging){
				$this->addCSS('debug');	
			}
        }else{
			return; 
		}		
    }
	
	public function _blank(){
		$this->integration = false;
	}
	
	public function fetch_css($debug = false, $exclusion = array()){
		//$this->generate_styles();

		$return = '<style>';
		foreach($this->_a_CSS as $a_css){
			if(!$debug && preg_match('/debug/', $a_css['file']) === 1){
				continue;
			}
			if(!empty($exclusion)){
				$flag = false;
				foreach($exclusion as $name){
					if(preg_match("/$name/", $a_css['file']) === 1){
						$flag = true;
						break;
					}
				}
				if($flag){
					continue;
				}
			}
					//var_dump($a_css['file']);
			$return .= file_get_contents($a_css['file'] . '.' . $a_css['extension']);
		}
		$return .= ' tbody:before, tbody:after { display: none; }';
		$return .= '</style>';
		//var_dump($return);
		self::_blank();
		return $return;
		//die();
	}
	
	public function checkDirectory($directory){
       if(!file_exists($directory)){
            $error = 'Compile/Cache directory "%s" is not writeable/executable';
            $error = sprintf($error, $directory);
        }
        
        return true;
    }

	public function _fetch_resource_info(&$params) { 
		// For some annoying reason, windows uses instead of / in paths and kohana doesn't change it
		if(strpos($params['resource_name'], str_replace('\\', '/', DOCROOT)) === FALSE) 
		
		$params['resource_name'] = $this->find_file($params['resource_name']); 

		return parent::_fetch_resource_info($params); 
	} 
		
	public static function find_file($file, $ext = NULL, $template = NULL) {
	 	//Si on n'a pas de template, on ouvre le dossier par d�fault qui est la base de views = ""
		$template = empty($template) ? Kohana::$config->load('smarty.default_templates') : $template; $ext = empty($ext) ? 'tpl' : $ext; 
		
		if(substr($file, -strlen($ext)) == $ext) $file = substr($file, 0, -(strlen($ext)+1)); 
		var_dump(kohana::find_file('views/' . $template, $file, TRUE, $ext));
		die();
		return kohana::find_file('views/' . $template, $file, TRUE, $ext); 
	} 	
	
	private function assignStartingValues($a_values){
		foreach($a_values as $key => $value){
			$this->smarty->assign($key, $value);
		}
	}
	
	private function getTemplatesPaths($appViewsPath){
		$modules = array();
		foreach(Kohana::modules() as $key => $value){
			$modules[] = $value . "views";
		}
		
		return array_merge($modules, array($appViewsPath));
	}
}

?>