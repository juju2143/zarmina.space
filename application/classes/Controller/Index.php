<?php

class Controller_Index extends Kohana_Controller_Smarty {

	public function action_index()
	{
		$lang = $this->request->param('lang');
		$blogs = array_diff(scandir(APPPATH.'views'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'blog', SCANDIR_SORT_DESCENDING), ['.','..']);
		array_walk($blogs, function(&$a, $b){
			$a = str_ireplace('.tpl', '', $a);
		});
		$chapitres = array_diff(scandir(APPPATH.'views'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'chapitres', SCANDIR_SORT_ASCENDING), ['.','..']);
		array_walk($chapitres, function(&$a, $b){
			$a = str_ireplace('.tpl', '', $a);
		});
		$this->smarty->assign('blogs', array_slice($blogs, 0, 5));
		$this->smarty->assign('chapitres', $chapitres);
		$this->smarty->assign('template', $lang.'/content/index.tpl');
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

	public function action_page(){
		$lang = $this->request->param('lang');
		$id = $this->request->param('id');

		$this->smarty->assign('id', $id);

		$this->smarty->assign('template', $lang.'/content/pages/'.$id.'.tpl');
	}

	public function action_blog()
	{
		$lang = $this->request->param('lang');
		$offset = $this->request->param('offset');
		$blogs = array_diff(scandir(APPPATH.'views'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'blog', SCANDIR_SORT_DESCENDING), ['.','..']);
		array_walk($blogs, function(&$a, $b){
			$a = str_ireplace('.tpl', '', $a);
		});
		$this->smarty->assign('blogs', array_slice($blogs, $offset, 10));
		if($offset-10>=0) $this->smarty->assign('prev', $offset-10);
		if($offset+10<count($blogs)) $this->smarty->assign('next', $offset+10);
		$this->smarty->assign('template', 'common/blog.tpl');
	}

	public function action_post(){
		$lang = $this->request->param('lang');
		$id = $this->request->param('id');

		$this->smarty->assign('id', $id);

		$this->smarty->assign('template', 'common/post.tpl');
	}
} // End Index
