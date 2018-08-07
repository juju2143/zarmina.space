<?php

class Controller_Index extends Kohana_Controller_Smarty {

	public function action_index()
	{
		$blogs = array_diff(scandir(APPPATH.'views'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'blog', SCANDIR_SORT_DESCENDING), ['.','..']);
		$chapitres = array_diff(scandir(APPPATH.'views'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'chapitres', SCANDIR_SORT_ASCENDING), ['.','..']);
		$chapitres = array_walk($chapitres, function(&$a, $b){
			$a = str_ireplace('.tpl', '', $a);
		});
		$this->smarty->assign('blogs', array_slice($blogs, 0, 5));
		$this->smarty->assign('chapitres', $chapitres);
		$this->smarty->assign('template', 'content/index.tpl');
	}

	public function action_phpinfo(){
		$this->_blank();
		phpinfo();
	}

	public function action_chapitre(){
		$id = $this->request->param('id');

		$this->smarty->assign('id', $id);

		$this->smarty->assign('template', 'common/chapitre.tpl');
	}
} // End Index
