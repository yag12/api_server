<?php
/**
* @Desc mysqli
*
*/
namespace Library\Model;

class Mysqli implements \Library\Model\InterfaceModel
{
	protected $db;
	protected $mysql;

	/**
	* @Desc construct
	* @Param Info.db $db
	* @Return void
	*/
	public function __construct(&$db)
	{
		$this->db = &$db;
		$this->connection();
	}

	/**
	* @Desc destruct
	*
	* @Return void
	*/
	public function __destruct()
	{
		$this->mysql->close();
	}

	/**
	* @Desc mongodb connect
	* @Param void
	* @Return void
	*/
	public function connection(){
		$host = $this->db['host'];
		$user = $this->db['user'];
		$passwd = $this->db['passwd'];
		$name = $this->db['name'];
		$port = $this->db['port'];

		$this->mysql = new \mysqli($host, $user, $passwd, $name, $port);
		if($this->mysql->connect_error)
		{
			\Library\Console::debug('error connect：'.$this->mysql->connect_error);
			throw new \Exception('error connect：'.$this->mysql->connect_error);
		}

		$this->initCharSet();
	}

	/**
	* @Desc mysql charset
	*
	* @Param $charSet
	*
	* @Return void
	*/
	public function initCharSet($charSet = 'utf8')
	{
		$sql = "SET NAMES " . $charSet;
		$stmt = $this->mysql->prepare($sql);

		if($stmt === false)
		{
			\Library\Console::debug('error set names : '.$this->mysql->error);
			throw new \Exception('error set names : '.$this->mysql->error);
		    return false;
		}

		$stmt->execute();
		$stmt->close();
	}

	/**
	* @Desc mysql query
	*
	* @Param string $sql
	*
	* @Return array
	*/
	public function query($sql = null)
	{
		$result = $this->mysql->multi_query($sql);
		\Library\Console::debug('sql query : ' . preg_replace("/;/i", ";\n", $sql));
		if($result == false)
		{
			\Library\Console::debug('error query : '.$this->mysql->error);
			throw new \Exception('error query : '.$this->mysql->error);
		    return false;
		}

		$i = 0;
		$returnObj = array();
		while(true)
		{
			$returnObj[$i] = array();
			if($result = $this->mysql->store_result())
			{
				$count = 0;
				while($row = $result->fetch_array(MYSQLI_ASSOC))
				{
					$returnObj[$i]['rows'][] = $row;
					$count++;
				}

				$returnObj[$i]['count'] = $count;
				$result->free();
			}

			if ($this->mysql->more_results())
			{
				$this->mysql->next_result();
			}
			else
			{
				break;
			}

			$i++;
		}

		return $returnObj;
	}

	/**
	* @Desc db select
	*
	*/
	public function find(){ }

	/**
	* @Desc db insert
	*
	* @Param string or array $data
	*
	* @Return boolean
	*/
	public function save($data = array())
	{
		if(empty($data)) return false;
		$sql = 'insert into ' . $this->name . ' set ';

		if(is_array($data))
		{
			$fields = null;
			foreach($data as $key=>$value)
			{
				$key = '`' . $key . '`';
				if(is_string($value)) $value = '\'' . $value . '\'';
				$fields = (!empty($fields) ? $fields . ',' : '') . $key . '=' . $value;
			}
			$sql = $sql . $fields . ';';
		}

		$this->query($sql);
		return true;
	}

	/**
	* @Desc db delete
	*
	*/
	public function remove(){ }
}
