<?php
if (!defined('SP_ROOT')) exit;
/**
* Controller class
*/

// TO DO:
// methods: join(table name, ON, type); group_by()
// fetch, fetch_row, fetch_var
//
class SP_Model
{	
	# Db handler
	private $_handle = null;

	# Array for query building
	private $_query = array();

	# Returns mysqli connection
	public function __construct()
	{
		$this->_handle = SP_DB::getInstance();
	}

	function select($fields = '*')
	{
		$model = new SP_Model;
		$model->_query['select'] = $fields;
		return $model;
	}

	public function from($table, $pseudonim = false)
	{
		if (!empty($pseudonim)) $table = $table . ' AS ' . $pseudonim;
		$this->_query['from'] = $table;
		return $this;		
	}

	public function where($conditions)
	{
		if (empty($conditions)) {
			return false;
		}

		if (!is_array($conditions)) {
			$conditions = array($conditions);
		}
		$where = '';
		foreach ($conditions as $condition => $value) {
			if (!is_numeric($condition)) {
				$where .= ' ' . $condition .' = `' .$this->_handle->real_escape_string($value). '` AND';
			} else {
				$where .= ' ' . $value;
			}
		}
		
		$where = rtrim($where, 'AND');
		$this->_query['where'] = $where;
		return $this;
	}

	public function or_where($conditions)
	{
		if (empty($conditions)) {
			return false;
		}

		if (!is_array($conditions)) {
			$conditions = array($conditions);
		}

		$where = '';
		foreach ($conditions as $condition => $value) {
			if (!is_numeric($condition)) {
				$where .= ' ' . $condition .' = `' .$this->_handle->real_escape_string($value). '` AND';
			} else {
				$where .= ' ' . $value;
			}
		}
		
		$where = rtrim($where, 'AND');
		$this->_query['or_where'] = $where;
		return $this;
	}

	public function order($field, $order = 'ASC')
	{
		$this->_query['order'] = ' ORDER BY ' . $field . ' ' . $order;
		return $this;
	}

	private function buildQuery()
	{
		if (empty($this->_query)) {
			return false;
		}

		$query = 'SELECT '.$this->_query['select'].' FROM '.$this->_query['from'].' WHERE ('.$this->_query['where'].')';
		if (array_key_exists('or_where', $this->_query)) {
			$query .= ' OR ('.$this->_query['or_where'].')';
		}
		if (!empty($this->_query['order'])) {
			$query .= $this->_query['order'];
		}

		return $query;
	}

	public function __tostring()
	{
		return $this->buildQuery();
	}

	public function insert($table, array $data)
    {
		if (empty($table) || empty($data)) {
			return false;
		}

    	$this->create();
		foreach ($data as $key => &$value) {
			if (empty($value)) {
				unset($data[$key]);
				continue;
			}
			$value = trim($this->_handle->real_escape_string($value));
		}

        $q = 'INSERT INTO '.$table.' (' . implode(', ', array_keys($data)) . ')
        	VALUES (`' . implode('`, `', $data) . '`)';

        if ($this->_handle->query($q)) {
	    	return $this->mysqli->insert_id();
        }

        return false;
    }

    public function update($table, array $data, array $where)
    {
		if (empty($table) || empty($data) || empty($where)) {
			return false;
		}

		$q = 'UPDATE '.$table.' SET';

		foreach ($data as $key => $value) {
			if (!empty($value)) {
				$q .= ' ' . $key .' = `' . trim($this->_handle->real_escape_string($value)) . '`, ';
			}
		}

		$q = rtrim($q, ',');
		$q .= ' WHERE';

		foreach ($where as $key => $value) {
			$q .= ' ' . $key .' = `' .$this->_handle->real_escape_string($value). '` AND';
		}

		$q = rtrim($q, 'AND');
		$this->_handle->query($q);
		return $this->_handle->affected_rows;
    }

    public function delete($table, array $where)
    {
		if (empty($table) || empty($where)) {
			return false;
		}

		$q = 'DELETE FROM '.$table.' WHERE';

		foreach ($where as $key => $value) {
			$q .= ' ' . $key .' = `' .$this->_handle->real_escape_string($value). '` AND';
		}

		$q = rtrim($q, 'AND');
		$this->_handle->query($q);
		return $this->_handle->affected_rows;
    }

    public function execute($q)
    {
    	if (empty($query)) {
    		return false;
    	}

    	$this->create();
    	$this->_handle->query($q);
		return $this->_handle->affected_rows;
    }
}

