<?php
class Medias extends Controller{
	
	protected $medias_active = 1;
	
	private $a_configs_controller = array(); 
	
	private $a_configs_page = array(); 
	
	private $a_configs_modules = array('scripts' => array(), 'styles' => array()); 
	
	private $a_configs_common = array();

	private $_parent_tpl = '';
	
	private $_templatecontentPage = "";

	protected $_a_CSS = array();

	private $_a_JavaScripts = array();

	private $_a_embedded_javascript = array();
	
	private $_a_modules = array();
	
	private $_title_uri_translation = NULL;
	
	private $_force_template = NULL;
	
	//Constructeur
	function before(){
		parent::__construct($this->request, $this->response);
		
		if($this->integration){
			//On va chercher les configurations de la page
			$this->checkConfigSite();
			$this->checkConfigModules();
			$this->checkConfigController();
			$this->checkConfigPage();
			$this->applyControllerSetting();
		}
	}
	
	//protected function generate_styles(){
//		$this->checkConfigSite();
//		$this->checkConfigModules();
//		$this->checkConfigController();
//		$this->checkConfigPage();
//		
//		//On génère les modules de jQuery de scripts avec les médias nécessaires
//		$this->applyControllerModules();
//		//On applique les styles configurés de la page
//		$this->applyControllerStyles();
//		
//		$this->genControllerSetting();
//	}
	
	public function medias_integration($template = NULL){
		if($this->integration){
			//On génère les configurations
			$this->genControllerSetting();
			
			//Si on ne recoit pas de template de content, on affiche le content vide
			if(!isset($this->smarty->tpl_vars['template'])){
				$this->smarty->assign('template', false);
			}
			
			//On va chercher le template
			if(!is_null($this->_force_template)){
				$this->_parent_tpl = $this->_force_template;
			}else if(!is_null($template)){
				$this->_parent_tpl = $template;
			}elseif(empty($this->_parent_tpl)){
				$this->_parent_tpl = Kohana::$config->load('smarty.index_template');
			}
		}
	}
	
	public function gen_template(){
		if($this->integration){
			$this->smarty->display($this->_parent_tpl);
		}
	}
	
	public function set_parent_tpl($parent_tpl){
		$this->_parent_tpl = $parent_tpl;
	}
	
	public function settitle_uritranslation($title_uri_translation = "", $route = "", $a_params = array()){
		if(!empty($route)){
			$this->_title_uri_translation .= $route . DIRECTORY_SEPARATOR;
		}
		
		//On enlève la langue des params
		if(isset($a_params['lang'])){
			unset($a_params['lang']);
		}
		
		if(!empty($a_params)){
			$params = implode($a_params, DIRECTORY_SEPARATOR);
		}else if(is_null($values)){
			$params = '';
		}
		
		//On remplace le title de la langue actuel par son opposé
		$uri = str_replace($params, $title_uri_translation, $params);
		
		$this->_title_uri_translation .= $title_uri_translation;
	}
	
	protected function force_template($template){
		$this->_force_template = $template;
	}

	//On regarde si on a des configuration du site
	private function checkConfigSite(){
		//On va chercher le fichier config du controller dans un répertoire du controller
		$a_fichierConfigcommon = Kohana::find_file('config', 'controller' . DIRECTORY_SEPARATOR . 'common');
		//Si le fichier existe, on y garde ses données
		if(!empty($a_fichierConfigcommon)) {
			//On va chercher ses paramètres directements dans le fichier
			$this->a_configs_common = require_once(end($a_fichierConfigcommon));
		} else {
			$this->a_configs_common = NULL;
		}
	}
	
	//On regarde si des modules actifs doivent avoir des médias d'initialisés
	private function checkConfigModules(){
		$a_modules = Kohana::modules();
		foreach($a_modules as $key => $value){
			//On va chercher le fichier config du controller dans un répertoire du controller
			$a_fichierConfigcommon = Kohana::find_file('config', str_replace('_', DIRECTORY_SEPARATOR, strtolower($key)));
			if(!empty($a_fichierConfigcommon)){
				$a_configs = require_once(end($a_fichierConfigcommon));
				
				//Si le fichier existe, on y garde ses données
				if(!empty($a_configs)){
					//On va chercher les scripts de chaques fichiers configs
					if (is_array($a_configs) && array_key_exists('medias', $a_configs)) {
						//On vérifit qu'on a bien des scripts de settés
						if(array_key_exists('scripts', $a_configs['medias'])){
							$this->a_configs_modules['scripts'] = array_merge($this->a_configs_modules['scripts'], $a_configs['medias']['scripts']);
						}
					}
					
					//On va chercher les styles de chaques fichiers configs
					if (is_array($a_configs) && array_key_exists('medias', $a_configs)) {
						//On vérifit qu'on a bien  des styles de settés
						if(array_key_exists('styles', $a_configs['medias'])){
							$this->a_configs_modules['styles'] = array_merge($this->a_configs_modules['styles'], $a_configs['medias']['styles']);
						}
					}
				}
			}
		}
	}
	
	//On va chercher le fichier config du controller en commençant par regarder si le controller a un dossier, sinon on regarde généralement	
	private function checkConfigController(){
		//On va chercher le fichier config du controller dans un répertoire du controller
		$a_fichierConfigcommon = Kohana::find_file('config', 'controller' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, strtolower($this->request->controller())) . DIRECTORY_SEPARATOR . 'common');
		//Si le fichier existe, on y garde ses données
		if(!empty($a_fichierConfigcommon)) {
			//On va chercher ses paramètres directements dans le fichier
			$this->a_configs_controller = require_once(end($a_fichierConfigcommon));
		}else if (Kohana::find_file('config', strtolower($this->request->controller()))) {
			$this->a_configs_controller = Kohana::$config->load(strtolower($this->request->controller()));
		} else {
			$this->a_configs_controller = NULL;
		}
	}

	//On regarde si on a des configurations de la page, seulement s'il y a un répertoire de configuration pour le controller
	private function checkConfigPage(){
		//On va chercher le fichier config de la page dans un répertoire du controller
		$a_fichierConfigPage = Kohana::find_file('config', 'controller' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, strtolower($this->request->controller())) . DIRECTORY_SEPARATOR . strtolower($this->request->action()));
		//Si le fichier existe, on y garde ses données
		if(!empty($a_fichierConfigPage)) {
			$this->a_configs_page = require_once(end($a_fichierConfigPage));
		}else{
			$this->a_configs_page = NULL;
		}
	}
			
	private function applyControllerSetting() {
		//On set les paramètres de la page pour smarty et le javascript
		$this->pageConfigs();
		
		//On regarde si on doit resetter les styles du navigateur
		$this->setResetStyles();
		
		//On regarde si on doit activer jQuery
		$start_jquery = Kohana::$config->load('smartymedias.startJquery');
		
		//On génère les modules de jQuery de scripts avec les médias nécessaires
		$this->applyControllerModules();
		
		//On applique les styles configurés de la page
		$this->applyControllerStyles();
		
		//On applique les scripts configurés de la page
		$this->applyControllerScripts();
	}
	
	private function setResetStyles(){
		if(Kohana::$config->load('smartymedias.resetStyles')){
			$this->addCSS("reset"); 
		}
	}
	
	//On envoit des informations
	private function pageConfigs(){
		//On configure les variables nécessaire à l'affichage de la page 
		$a_configs = array(
		   'lang' 	  		=> strtolower(Request::current()->param('lang')),
		   'user_id_lang'		=> Helper_User::get_id_lang(),
		   'controller'   		=> strtolower($this->request->controller()),
		   'action' 		  	=> strtolower($this->request->action()),
		   'url_medias'   		=> Kohana::$config->load('appconf.url_medias'),
		   'url_base'    		=> URL::base(),
		   'url_lang'     		=> URL::lang(),
		   'url_now'     		=> URL::now(),
		   'url_domain_lang'    => URL::now(true),
		   'page_referer' 		=>  ($this->request->referrer() == URL::now() ? null : $this->request->referrer()),
		);

		//On génère les informations à envoyer à javascript
		$this->addEmbeddedJavascript(Embeddedjavascript::encode_var_to_js($a_configs));	
		
		//On génère les données pour smarty
		$this->setMultiAssign($a_configs);
	}
	
	private function applyControllerStyles(){
		$styles_dir = Kohana::$config->load('appconf.url_medias') . Kohana::$config->load('appconf.path_styles');

		//On va chercher les styles des modules
		if(count($this->a_configs_modules['styles']) > 0){
			foreach($this->a_configs_modules['styles'] as $module){
				$this->addCSS($module);
			}
		}
		
		//On va chercher les styles de page commons
		if (is_array($this->a_configs_common) && array_key_exists('styles', $this->a_configs_common)) {
			$this->addCSS($this->a_configs_common['styles'], $styles_dir);
		}

		//On assigne les styles du controller
		if (is_array($this->a_configs_controller) && array_key_exists('styles', $this->a_configs_controller)) {
			$this->addCSS($this->a_configs_controller['styles']);
		}	

		//On assigne les styles propres à la page
		if (is_array($this->a_configs_page) && array_key_exists('styles', $this->a_configs_page)) {
			$this->addCSS($this->a_configs_page['styles']);
		}
	}
	
	private function applyControllerScripts(){
		$scripts_dir = Kohana::$config->load('appconf.url_medias') . Kohana::$config->load('appconf.path_scripts');
			
		//On va chercher les scripts des modules
		if(count($this->a_configs_modules['scripts']) > 0){
			foreach($this->a_configs_modules as $module){
				$this->addJavaScripts($module);
			} 
		}
			
		//On adde les scripts commons du site
		if(is_array($this->a_configs_common) && array_key_exists('scripts', $this->a_configs_common)){
			//On adde le script
			$this->addJavaScripts($this->a_configs_common['scripts'], $scripts_dir);
		}

		//On va chercher les scripts du controller
		if(is_array($this->a_configs_controller) && array_key_exists('scripts', $this->a_configs_controller)){
			$this->addJavaScripts($this->a_configs_controller['scripts']);
		}
	
		//On va chercher les scripts propres à la pages
		if(is_array($this->a_configs_page) && array_key_exists('scripts', $this->a_configs_page)){
			$this->addJavaScripts($this->a_configs_page['scripts']);
		}
	}
	
	private function applyControllerModules(){
		//On va chercher la list des modules du site
		if(is_array($this->a_configs_common) && array_key_exists('modules', $this->a_configs_common)){
			$this->addModules($this->a_configs_common['modules']);
		}
		
		//On va chercher la list des modules du controller
		if(is_array($this->a_configs_controller) && array_key_exists('modules', $this->a_configs_controller)){
			$this->addModules($this->a_configs_controller['modules']);
		}
		
		//On va chercher les scripts propres à la pages
		if(is_array($this->a_configs_page) && array_key_exists('modules', $this->a_configs_page)){
			$this->addModules($this->a_configs_page['modules']);
		}
		
		//Si on a un ou plusieurs modules
		if(count($this->_a_modules) > 0){
			//Pour chaque module, on va chercher ses médias nécessaires
			foreach($this->_a_modules as $value){
				//On va chercher le fichier config du module
				if (Kohana::find_file('config' . DIRECTORY_SEPARATOR . 'medias', $value)) {
					$config_path = Kohana::find_file('config' . DIRECTORY_SEPARATOR . 'medias', $value);
					$config_file= require_once($config_path);
					if( array_key_exists('medias', (array)$config_file)){   //
						$configs_module = $config_file['medias'];
					
						//On adde les scripts nécessaires
						if(array_key_exists('scripts', $configs_module)){
							
							$this->addJavaScripts($configs_module['scripts']);
						}
						
						//On adde les styles nécessaires
						if(array_key_exists('styles', $configs_module)){
							$this->addCSS($configs_module['styles']);
						}
					}	
				}
			}
		}
	}
	
	public function setMultiAssign($a_values){
		foreach($a_values as $key => $value){
			$this->smarty->assign($key, $value);
		}
	}

	public function setTemplatePage($template){
		die($template);
		$smarty_ext = Kohana::$config->load('smarty.templates_ext');

		$filepath = Kohana::find_file('views' . DIRECTORY_SEPARATOR . 'content', $template, FALSE, $smarty_ext);
		
		if($filepath === FALSE){
			$filepath = Kohana::find_file('views', $template, FALSE, $smarty_ext);
		}

		if($filepath === FALSE){
			throw new Kohana("Template content introuvable! Chemin Template : ".$template);
		}

		$this->_templatecontentPage = $filepath !== FALSE ? $filepath : $template;
	}
	
	//On set les styles de la page
	public function setCSS($a_css){
		$this->_a_CSS = $a_css;
	}
	
	public function addCSS($css, $dir = NULL){
		if ( !is_array($css)) {
			$css = array($css);
		}
		
		$new_css = array();
		
		//On vérifit l'existance de chaque fichier
		foreach($css as $key => $fichier){
			//Pour chaque styles à adder, on adde le chemin par défaut si elle n'est pas déjà présente
			if(strrpos($fichier, "http") === false && strrpos($fichier, "www") === false){
				if(is_null($dir)){
					//On regarde si le fichier existe avant de l'adder
					$pathfile = Kohana::find_file('medias', Kohana::$config->load("smartymedias.url_styles") . $fichier, "css");

					if(!empty($pathfile)){
						//On génère le chemin relatif
						$relativePathFile = $this->absoluteToRelativePath($pathfile);
						$new_css[] = array('file' =>  $relativePathFile, 
										   'rel'  => 'stylesheet',
									  	   'extension' => 'css',
										   'date' => filemtime($pathfile));
					}
				}else if($pathfile = Kohana::find_file('medias',  Kohana::$config->load('appconf.path_styles') . $fichier, "css")){
					$filemtime = filemtime($pathfile);
					
					$new_css[] = array('file' =>  $this->formatFilePath($dir . $fichier), 
									   'rel'  => 'stylesheet',
									   'extension' => 'css',
									   'date' => filemtime($pathfile));
				}else{
					
				}
			
				//On regarde si une version less existe pour l'adder
				if(is_null($dir)){
					$pathfile = Kohana::find_file('medias', Kohana::$config->load("smartymedias.url_styles") . $fichier, "less");

					if(!empty($pathfile)){
						//On génère le chemin relatif
						$relativePathFile = $this->absoluteToRelativePath($pathfile);
						$new_css[] = array('file' =>  $relativePathFile, 
										   'rel'  => 'stylesheet/less',
									  	   'extension' => 'less',
										   'date' => filemtime($pathfile));
					}
				}else if($pathfile = Kohana::find_file('medias',  Kohana::$config->load('appconf.path_styles') . $fichier, "less")){
					$filemtime = filemtime($pathfile);
					$new_css[] = array('file' =>  $this->formatFilePath($dir . $fichier), 
									   'rel'  => 'stylesheet/less',
									   'extension' => 'less',
									   'date' => filemtime($pathfile));
				}	
			}else{
				$new_css[] = $this->formatFilePath($fichier);
			}	
		}

		$this->_a_CSS = $this->adderValeurTableau($this->_a_CSS, $new_css);
	}
	
	//On set les modules de la page
	public function setModule($a_modules){
		$this->_a_modules = $a_css;
	}
	
	public function addModules($modules){
		if ( !is_array($modules)) {
			$modules = array($modules);
		}
		
		$a_nouveauxModules = $this->adderValeurTableau($this->_a_modules, $modules);

		$this->_a_modules = $a_nouveauxModules;
	}
	
	//On configure la list des JS à afficher
	public function setJavaScripts($array){
		 $this->_a_JavaScripts = $array;
	}

	public function addJavaScripts($javascript, $dir = NULL){
		if (!is_array($javascript)) {
			$javascript = array($javascript);
		}

		$new_scripts = array();
		
		//On vérifit l'existance de chaque fichier
		foreach($javascript as $key => $fichier){
			//Pour chaque styles à adder, on adde le chemin par défaut si elle n'est pas déjà présente
			if(strrpos($fichier, "http") === false && strrpos($fichier, "www") === false){
				if(is_null($dir)){
					//On regarde si le fichier existe avant de l'adder
					$pathfile = Kohana::find_file('medias', Kohana::$config->load("smartymedias.url_scripts") . $fichier, "js");
			
					if(!empty($pathfile)){
						//On génère le chemin relatif
						$relativePathFile = $this->absoluteToRelativePath($pathfile);
						$new_scripts[] = array('file' =>  $relativePathFile, 
											   'date' => filemtime($pathfile));
					}
				}else if($pathfile = Kohana::find_file('medias',  Kohana::$config->load('appconf.path_scripts') . $fichier, "js")){
					$filemtime = filemtime($pathfile);
					$new_scripts[] = array('file' =>  $this->formatFilePath($dir . $fichier), 
										   'date' => filemtime($pathfile));
				}
			}else{
				$new_scripts[] = $this->formatFilePath($fichier);
			}
		}

		$this->_a_JavaScripts = $this->adderValeurTableau($this->_a_JavaScripts, $new_scripts);
	}

	public function addEmbeddedJavascript($javascriptCode) {
		if (!is_array($javascriptCode)) {
			$javascriptCode = array($javascriptCode);
		}

		$this->_a_embedded_javascript = $this->adderValeurTableau($this->_a_embedded_javascript, $javascriptCode);
	}
	
	public function add_array_to_object_js($nom_variable, $array) {
		$javascriptCode = 'var ' . $nom_variable . ' = {' . "\n";
		
		foreach($array as $key => $value){
			$javascriptCode .= ' ' . $key . " : '" . addslashes($value) . "'";
			
			if ($value !== end($array)){
				$javascriptCode .= ", " . "\n";
			}
		}
		
		$javascriptCode .= '};';

		if (!is_array($javascriptCode)) {
			$javascriptCode = array($javascriptCode);
		}

		$this->_a_embedded_javascript = $this->adderValeurTableau($this->_a_embedded_javascript, $javascriptCode);
	}

	public function addValueArrayAssign($variable, $cle, $valeur){
		$this->smarty->assign($variable, array_merge($this->get_template_vars($variable), array($cle=>$valeur)));
	}
	
	//On exécute l'add de données dans la table si la valeur n'est pas déjà là seulement
	private function adderValeurTableau($a_anciensElements, $a_nouveauxElements){
		//On fusionne les deux lists qu'on a 
		$a_elementsFusionnes = array_merge($a_anciensElements,$a_nouveauxElements);

		//On retourne l'article list sans doublons
		return $this->check_doublons_array($a_elementsFusionnes);
	}
	
	private function check_doublons_array($a_items){
		$a_result = array_map("unserialize", array_unique(array_map("serialize", $a_items)));
		
		return $a_result;
	}
	
	private function genControllerSetting(){
		if(count($this->_a_CSS) != 0){
			$this->smarty->assign('a_CSS', $this->_a_CSS);	
		}
		
		if(count($this->_a_JavaScripts) != 0){
			$this->smarty->assign('a_script', $this->_a_JavaScripts);	
		}
		
		if(count($this->_a_embedded_javascript) != 0){
			$this->smarty->assign('a_embedded_javascript', $this->_a_embedded_javascript);	
		}
		
		//Template à utiliser pour le content de la page
		if(!empty($this->_templatecontentPage)){
			$this->smarty->assign('templatecontent', $this->_templatecontentPage);
		}
		
		if(isset($_SESSION['alert']) && !empty($_SESSION['alert'])){
			$this->smarty->assign('alert', $_SESSION['alert']);
		}
	}

	public function absoluteToRelativePath($abspath){	
		//On enlève le chemin de la base de notre chemin de fichier trouvé
		$relativePath = explode(DOCROOT . 'medias' . DIRECTORY_SEPARATOR, $abspath);

		//Si on a plusieurs éléments, on va dans l'application, sinon dans les modules			
		if(strpos($abspath, "modules") === false){
			$relativePath = Kohana::$config->load('appconf.url_medias') . end($relativePath);
		}else{
			//On va chercher le restant de la chaine qui est après le répertoire "modules"
			$pos = strpos($abspath, 'modules');
			$chemin = substr($abspath, $pos + strlen('modules' . DIRECTORY_SEPARATOR));
			$relativePath = MEDIASPATH . $chemin;
		}	
		
		//On retourne le chemin formaté comme il faut si on a un résultat
		if(!empty($relativePath)){
			return $this->formatFilePath($relativePath, true);
		}else{
			return null;	
		}			
	}

	public static function formatFilePath($path, $deleteExt = false){
		//On format le lien comme il faut
		$result = str_replace('\\', '/', $path);
		
		//On enlève l'extension du fichier
		if($deleteExt){
			$result = preg_replace("/\\.[^.\\s]{2,4}$/", "", $result);
		}
		
		return $result;
	}
}
?>