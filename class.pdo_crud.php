<?php
class PDO_CRUD
{
	/*
	*
	*	PHP >= 5.4.0 IS REQUIRED
	*
	*/

	public $s;				// The database connection that we set in the constructor [pdo]
	public $_query  = null;	// Where we store the query(s)
	public $pdo 	= null; // The database settings [ini]
	public $params  = [];
	
	public function __construct($ini_file)
	{ 
		try
		{
			$this->pdo = parse_ini_file($ini_file);
			$_pdoAttr  = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
			$this->s   = new PDO("mysql:host={$this->pdo['host']};dbname={$this->pdo['dbname']}", $this->pdo['user'], $this->pdo['pass'], $_pdoAttr);
		}
		catch (PDOException $e)
		{
			exit($e->getMessage());
		}
	}

	public function __destruct()
 	{
 		$this->s = null;
 	}
	
	public function antiXSS($str)
	{
		$filtered = null;

		if(is_array($str))
		{
			foreach($str as $val)
			{
				$filtered .= trim(strip_tags(htmlspecialchars($val, ENT_QUOTES, 'utf-8')));
			}
		}
		else
		{
			$filtered = trim(strip_tags(htmlspecialchars($str, ENT_QUOTES, 'utf-8')));
		}
		return $filtered;
	}

	public function _init($query, $params)
	{
		try {
			// Lets prepare our query
			$this->_query = $this->s->prepare($query);

			// Add's the params to our $params array
			$this->bindAll($params);

			// Now we bind the params
			if(!empty($this->params))
			{
				foreach($this->params as $p)
				{
					$parameters = explode("\_NEW", $p);
					$this->_query->bindParam($parameters[0],$this->antiXSS($parameters[1]));
				}		
			}

			// Lets run the query
			$this->_query->execute();		
		}
		catch(PDOException $e)
		{
			// Oh snap, something went wrong!
			exit($e->getMessage());
		}

		$this->params = [];	// Resets the params
		return $this;
	}
	
	public function query($query, $params = null, $Fmode = PDO::FETCH_ASSOC)
	{
		$query = trim($query);
		$this->_init($query, $params);
		$stat  = strtolower(substr($query, 0 , 6));
		
		switch($stat)
		{
			case 'select':
				return $this->_query->fetchAll($Fmode);
			break;
			case 'insert':
			case 'update':
			case 'delete':
				return $this->_query->rowCount();
			break;
			default:
				return null;
			break;
		}
	}

	public function column($query, $params = null)
	{
		$columns = $this->_init($query, $params)->_query->fetchAll(PDO::FETCH_NUM);		
		$column = null;

		foreach($columns as $cols)
		{
			$column[] = $cols[0];
		}

		return $column;
	}

	public function bindAll($val)
	{
		if(empty($this->params) && is_array($val))
		{
			$columns = array_keys($val);
			
			foreach($columns as $exc => &$col)
			{
				$this->bind($col, $val[$col]);
			}
		}
		return $this;
	}

	public function bind($prm, $val)
	{	
		$this->params[sizeof($this->params)] = ":" . $prm . "\_NEW" . $val;	// sizeof() == count()
		return $this;
	}

	public function count($query, $params = null)
	{
		return intval($this->_init($query, $params)->_query->fetchColumn());
	}

	public function lastInsertId()
	{
		return intval($this->s->lastInsertId());
	}

	public function row($query, $params = null, $Fmode = PDO::FETCH_ASSOC)
	{
		return $this->_init($query, $params)->_query->fetch($Fmode);			
	}
     
	public function single($query, $params = null)
	{
		return $this->_init($query, $params)->_query->fetchColumn();
	}
/*

	public static function throwError($err)
	{
		try
		{
			throw new Exception($err);
		}
		catch(Exception $error)
		{
			exit($error->getMessage());
		}
	}

*/			
}