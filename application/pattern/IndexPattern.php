<?php
/**
* Main Pattern (Front Controller)
*/
class IndexPattern extends SP_Controller
{	
	public $no_render = false;
	public $title;

	public function __construct(){
		parent::__construct();
	}


	public function display($view = 'index')
	{		
		$content = $this->view->render($view);
		if ($this->no_render) {
			echo $this->collapse($content);
			return true;
		}
		
		$this->view->title 			= $this->title;
		$this->view->content 		= $content;		
		echo $this->collapse($this->view->render('pattern/index'));

		return true;
	}	
	


}