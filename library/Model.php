<?php
/**
* @Desc model
*
*/
namespace Library;

abstract class BaseModel
{
	static $connected = array();
	protected $db = null;
	protected $type = 'default';

	/**
	* @Desc : Construct
	* @Param : Database $db
	* @Param : mixed $type
	* @Return : void
	*/
	public function __construct(&$db = null, &$type = null)
	{
		if(!empty($type))
		{
			$this->type = &$type;
		}

		if(empty(\Library\BaseModel::$connected[$this->type]))
		{
			if(!empty($db[$this->type]))
			{
				$this->db = &$db[$this->type];

				if(!empty($this->db))
				{
					if(\Library\Dispatcher::import('library.Model.InterfaceModel'))
					{
						$obj = null;
						switch($this->db['type'])
						{
							case 'mongo':
								if(\Library\Dispatcher::import('library.Model.Mongo'))
								{
									$this->db['obj'] = new \Library\Model\Mongo($this->db);
								}

								break;
							case 'mysqli':
								if(\Library\Dispatcher::import('library.Model.Mysqli'))
								{
									$this->db['obj'] = new \Library\Model\Mysqli($this->db);
								}

								break;
						}
					}

					\Library\BaseModel::$connected[$this->type] = &$this->db;
				}
			}
			else
			{
				throw new \Exception(\Config\Result::MSG_ERROR_NOT_DB_CONNECTED, \Config\Result::ERROR_NOT_DB_CONNECTED);
			}
		}
		else
		{
			$this->db = &\Library\BaseModel::$connected[$this->type];
		}
	}

	/**
	* @Desc query
	*
	* @Param $sql
	*
	* @Return mixed
	*/
	final protected function query($sql = null)
	{
		return $this->db['obj']->query($sql);
	}

	/**
	* @Desc : db table select
	* @Param : array $data
	* @Return : array or null
	*/
	final protected function find($data = array())
	{
		extract($data, EXTR_PREFIX_SAME, 'find');
		$result = array();

		return $result;
	}

	/**
	* @Desc : db table insert or update
	* @Param : array $data
	* @Return : int
	*/
	final protected function save($data = array())
	{
		return $this->db['obj']->save($data);
		//extract($data, EXTR_PREFIX_SAME, 'save');

		//return true;
	}

	/**
	* @Desc : db table delete
	* @Param : array $data
	* @Return : boolean
	*/
	final protected function rm($data = array())
	{
		extract($data, EXTR_PREFIX_SAME, 'rm');

		return true;
	}
	
}
