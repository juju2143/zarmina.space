<?php defined('SYSPATH') or die('No direct script access.');
	class Controller extends Kohana_Controller{

		public function __construct($request, $response){
			parent::__construct($request, $response);
			
			$lang = $this->request->param('lang');
			
			//Fetch languages list
			$this->get_lst_lang();
						
			//Define the max filesize for the uploads
			$this->set_max_upload_file();
		}

		public function get_lst_lang(){
			if(isset($this->integration) && $this->integration) {
				//Fetch languages list
				//$lang = new Lang();
				//$a_lang = $lang->get_lst_lang();
				
				//$this->smarty->assign('a_lang', $a_lang);	
			}
		}

		private function set_max_upload_file(){
			if(isset($this->integration) && $this->integration) {
				$max_filesize = Helper_Str::convert_filesize(ini_get('upload_max_filesize'));
				$max_filesize = $max_filesize / 1024 / 1024;
				$this->smarty->assign('max_filesize', $max_filesize);	
			}
		}

		public function after($template = NULL){
			//Instenciate the template engine
			if(isset($this->medias_active) && $this->medias_active){
				$this->medias_integration($template);
			}
			
			//Generate the important data concerning the hierarchy generator
			if(isset($this->hierarchy_active) && $this->hierarchy_active){
				$this->hierarchy_integration();
			}
			
			//Once everything else has been set, the page can be generated
			if(method_exists($this, 'gen_template')){
				$this->gen_template();
			}
			
			/*$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$finish = $time;
			$total_time = round(($finish - STARTTIME), 4);*/
		}
	}
?>