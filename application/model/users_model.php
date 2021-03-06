<?php
if (!defined('SP_ROOT')) exit;

class Users_model extends SP_Model
{
	public function fetch_example()
	{
		$a = $this->select('field1, field2')
			->from('table')
			->where(array('field3' => 17, 'field5' => '12'))
			->order('ord_id', 'DESC');

		//echo $a to dump query
		//return $a->fetchRow();
		//return $a->fetchVar();
		return $a->fetchAll();
	}

	public function delete_example()
	{
		return $this->delete('table', array('key' => 'value'));
	}

	public function insert_example()
	{
		return $this->insert('cart', array('usr_id' => 11, 'prd_id' => '12'));
	}

	public function update_example()
	{
		return $this->update('cart', array('ord_fancy_object' => 'asasas111a11'), array('prd_id' => 12));
	}

	public function join_example()
	{
		$a = $this->select('field1, field2')
			->from('table', 'a')
			->join(array('table_2','b'), 'a.field1 = b.test')
			->where(array('field1' => 17))
			->groupBy('min(field3), DESC')
			->order('ord_id, DESC');
		return $a->fetchAll();
	}
}