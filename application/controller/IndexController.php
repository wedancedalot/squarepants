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
		$this->users_model->update_example();

		$this->view->info = 'Some value';
		$this->title = 'Squarepants WELCOME!';
		$this->display('index');
	}	
}