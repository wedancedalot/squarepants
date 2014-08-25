<?php
if (!defined('SP_ROOT')) exit;

/**
* Model class. Used for query bulding and processing
*/
class SP_Model
{	
	# Db handler
	private $_handle = null;

	# Array for query building
	private $_query = array();

	# Returns DB handler
	public function __construct()
	{
		$this->_handle = SP_Database::init();
	}
	
	# Methods for quiery building
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

		$where = '';

		if (is_array($conditions)) {
			foreach ($conditions as $condition => $value) {
				$where .= ' ' . $condition .' = "' .$this->_handle->sanitize($value). '" AND';
			}
		} else {
			$where .= ' ' . $value;
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


		$where = '';

		if (is_array($conditions)) {
			foreach ($conditions as $condition => $value) {
				$where .= ' ' . $condition .' = "' .$this->_handle->sanitize($value). '" AND';
			}
		} else {
			$where .= ' ' . $value;
		}
		
		$where = rtrim($where, 'AND');
		$this->_query['or_where'] = $where;
		return $this;
	}

	public function order($field, $order = 'ASC')
	{
		$this->_query['order'] = 'ORDER BY ' . $field . ' ' . $order;
		return $this;
	}

	public function join($table, $on, $type  = 'LEFT')
	{
		# Pseudonim can be assigned as an array
		if (is_array($table)) {
			$table = $table[0] . ' AS ' . $table[1];
		}

		$this->_query['join'][] = $type . ' JOIN ' . $table . ' ON ' . $on;
		return $this;
	}

	public function groupBy($fields)
	{
		$this->_query['group_by'] = 'GROUP BY ' . $fields;
		return $this;
	}

	private function buildQuery()
	{
		if (empty($this->_query) || empty($this->_query['select']) || empty($this->_query['from'])) {
			return false;
		}

		$query = 'SELECT '.$this->_query['select'] . PHP_EOL .'FROM '.$this->_query['from'];

		# Join
		if (array_key_exists('join', $this->_query)) {
			foreach ($this->_query['join'] as $join) {
				$query .= PHP_EOL . $join;
			}
		}

		# Where
		if (array_key_exists('where', $this->_query)) {
			$query .= PHP_EOL . 'WHERE ('.$this->_query['where'].')';
		}

		# Or where
		if (array_key_exists('or_where', $this->_query)) {
			$query .= PHP_EOL . 'OR ('.$this->_query['or_where'].')';
		}

		# Group by
		if (!empty($this->_query['group_by'])) {
			$query .= PHP_EOL . $this->_query['group_by'];
		}

		# Order
		if (!empty($this->_query['order'])) {
			$query .= PHP_EOL . $this->_query['order'];
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

		foreach ($data as $key => &$value) {
			if (empty($value)) {
				unset($data[$key]);
				continue;
			}
			$value = trim($this->_handle->sanitize($value));
		}

        $q = 'INSERT INTO '.$table.' (' . implode(', ', array_keys($data)) . ')
        	VALUES ("' . implode('", "', $data) . '")';
		$this->_handle->execute($q);
		return $this->_handle->insert_id();
    }

    public function update($table, array $data, array $where)
    {
		if (empty($table) || empty($data) || empty($where)) {
			return false;
		}

		$q = 'UPDATE '.$table.' SET';

		foreach ($data as $key => $value) {
			if (!empty($value)) {
				$q .= ' ' . $key .' = "' . trim($this->_handle->sanitize($value)) . '", ';
			}
		}

		$q = rtrim($q, ', ');
		$q .= ' WHERE';

		if (is_array($where)) {
			foreach ($where as $key => $value) {
				$q .= ' ' . $key .' = "' .$this->_handle->sanitize($value). '" AND';
			}
		} else {
			$q .= ' '.$where;
		}

		$q = rtrim($q, 'AND');
		$this->_handle->execute($q);
		return $this->_handle->affected();
    }

    public function delete($table, $where)
    {
		if (empty($table) || empty($where)) {
			return false;
		}

		$q = 'DELETE FROM '.$table.' WHERE';

		if (is_array($where)) {
			foreach ($where as $key => $value) {
				$q .= ' ' . $key .' = "' .$this->_handle->sanitize($value). '" AND';
			}
		} else {
			$q .= ' '.$where;
		}

		$q = rtrim($q, 'AND');
		$this->_handle->execute($q);
		return $this->_handle->affected();
    }

    public function fetchRow()
    {
		$query = $this->buildQuery();
    	if (empty($query)) {
    		return false;
    	}

    	return $this->_handle->fetchRow($query);
    }

    public function fetchAll()
    {
    	$query = $this->buildQuery();
    	if (empty($query)) {
    		return false;
    	}

    	return $this->_handle->fetchAll($query);
    }

	public function fetchVar()
    {
    	$query = $this->buildQuery();
    	if (empty($query)) {
    		return false;
    	}

    	return $this->_handle->fetchVar($query);
    }
}