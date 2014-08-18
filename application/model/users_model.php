<?php
if (!defined('SP_ROOT')) exit;

class Users_model extends SP_Model
{
	public function fetchAll()
	{
		$a = $this->select('id, name')
			->from('users', 'usr')
			->where(array('usr_created' => 12, 'testtest' => '14'))
			->order('test');

		$b = $this->select()
			->from('123', 'usr')
			->where('qwe = 12')
			->order('test', 'DESC');
		/* Still needs to be added */
	}
}