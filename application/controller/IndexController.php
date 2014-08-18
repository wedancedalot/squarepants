<?php
if (!defined('SP_ROOT')) exit;
class IndexController extends IndexPattern
{	
	public function __construct(){
		parent::__construct();
		$this->load('users_model');		
	}

	public function indexAction()
	{
		$a = $this->users_model->fetchAll();

		$this->view->info = 'Some value';
		$this->title = 'Squarepants WELCOME!';
		$this->display('index');
	}	
}