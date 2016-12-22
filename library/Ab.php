<?php
/**
* @Desc controller
*/
namespace Library;

abstract class Ab
{
	public $__SET_CONFIG__ = true;
	protected $config = array();

	/**
	* @Desc set config
	*
	* @Param $config
	*
	* @Return void
	*/
	final public function setConfig($config = array())
	{
		$this->config = &$config;
	}

	/**
	* @Desc import
	*
	* @Param string $name
	*
	* @Return boolean
	*/
	final static public function import($name = null)
	{
		return \Library\Dispatcher::import($name);
	}

	/**
	* @Desc get model
	*
	* @Param $name
	* @Param $number
	*
	* @Return model
	*/
	final protected function getModel($name = null, $number = -1)
	{
		$tmp = null;
		$split_name = explode('.', $name);
		foreach($split_name as $split)
		{
			$tmp = (!empty($tmp) ? $tmp . '.' : '') . ucfirst(strtolower($split));
		}
		$name = !empty($tmp) ? $tmp : $name;

		$model = null;
		if($this->import('library.Model'))
		{
			$type = null;
			$key = strtolower($name);
			$number = $number > -1 ? $number : $this->guid;
			$name = ucfirst($name);
			if($number > -1 && !empty($this->config->db_shard[$key]))
			{
				$shard = $number % sizeof($this->config->db_shard[$key]);
				$type = $this->config->db_shard[$key][$shard];
			}

			$importName = 'model.' . $name;
			$objectKey = $importName . (!empty($type) ? '' . $type : '');
			if(!empty(\Library\BaseController::$object[$objectKey]))
			{
				$model = \Library\BaseController::$object[$objectKey];
			}
			elseif($this->import($importName))
			{
				$name = str_replace('.', '\\', $name);
				$modelName = '\\Model\\' . $name . 'Model';
				\Library\BaseController::$object[$objectKey] = $model = new $modelName($this->config->db, $type);
			}
		}

		return $model;
	}

	/**
	* @Desc get models
	*
	* @Param $name
	*
	* @Return array
	*/
	final protected function getModelList($name = null)
	{
		$models = array();

		$key = strtolower($name);
		if(!empty($this->config->db_shard[$key]))
		{
			$db_keys = array_keys($this->config->db_shard[$key]);
			foreach($db_keys as $number)
			{
				$models[] = $this->getModel($name, $number);
			}
		}
		else
		{
			$models[] = $this->getModel($name);
		}

		return $models;
	}

	/**
	* @Desc get plugin
	*
	* @Param $name
	*
	* @Return plugin
	*/
	final protected function getPlugin($name = null)
	{
		$tmp = null;
		$split_name = explode('.', $name);
		foreach($split_name as $split)
		{
			$tmp = (!empty($tmp) ? $tmp . '.' : '') . ucfirst(strtolower($split));
		}
		$name = !empty($tmp) ? $tmp : $name;

		$plugin = null;
		$objectKey = $importName = 'plugin.' . $name;
		if(!empty(\Library\BaseController::$object[$objectKey]))
		{
			$plugin = \Library\BaseController::$object[$objectKey];
		}
		elseif($this->import('plugin.' . $name))
		{
			$name = str_replace('.', '\\', $name);
			$pluginName = '\\Plugin\\' . $name;
			\Library\BaseController::$object[$objectKey] = $plugin = new $pluginName;
			if(!empty($plugin->__SET_CONFIG__))
			{
				$plugin->setConfig($this->config);
			}
		}

		return $plugin;
	}

	/**
	* @Desc get cache
	*
	* @Param $name
	* @Param $number
	*
	* @Return cache or false
	*/
	final protected function getCache($name = null, $number = -1)
	{
		if(empty($name)) $name = \Config\Define::CACHE_DEFAULT_NAME;
		if(!empty($this->config->cache_shard[$name]))
		{
			$cache_shard = $this->config->cache_shard[$name];
			$type = null;
			if(is_array($cache_shard))
			{
				if(!empty($this->guid))
				{
					$number = $number > -1 ? $number : $this->guid;
				}
				else
				{
					$number = $number > -1 ? $number : array_rand($cache_shard);
				}
				
				$shard = $number % sizeof($cache_shard);
				$type = $cache_shard[$shard];
			}
			elseif(is_string($cache_shard))
			{
				$type = $cache_shard;
			}

			$cache = \Library\Dispatcher::cache($type);
			if(!empty($cache))
			{
				return $cache;
			}
		}
		
		return false;
	}

	/**
	* @Desc get game data
	*
	* @Param $tbName
	* @Param $tbIndex
	*
	* @Return mixed
	*/
	final protected function getGameData($tbName = null, $tbIndex = 0)
	{
		if(empty($tbName)) return null;

		$tbl = 'tbl';
		$data = null;
		$game_key = $tbName . ':' . $tbIndex;
		if(!empty(\Library\BaseController::$gameData[$game_key]))
		{
			return \Library\BaseController::$gameData[$game_key];
		}

		$game_data_type = defined('GAME_DATA_TYPE') ? GAME_DATA_TYPE : 0;
		// 게임 데이터 파일
		if($game_data_type === 0)
		{
			$tbl_file = TBL_DIR . $tbl . '_' . $tbName . '.json';
			if(is_file($tbl_file))
			{
				$tbl_json = file_get_contents($tbl_file);
				$tbl_data = !empty($tbl_json) ? json_decode($tbl_json, true) : array();
				if(!empty($tbl_data))
				{
					$data = $tbIndex > 0 ? (!empty($tbl_data[$tbIndex]) ? $tbl_data[$tbIndex] : array()) : $tbl_data;
				}
			}
		}
		// 게임 데이터 캐쉬
		else
		{
			$cache = $this->getCache('game_data');
			if(!empty($cache))
			{
				if(!empty(\Config\Define::CACHE_GAME_DATA_DB) && $cache->getType() == 'redis')
				{
					$cache->getRefer()->select(\Config\Define::CACHE_GAME_DATA_DB);
				}

				$bKey = $tbl . ':' . str_replace('_', ':', $tbName);
				$keys = array();
				$idx = $cache->get($bKey . ':idx');
				//$idxValue = msgpack_unpack($idx);
				$idxValue = json_decode($idx, true);
				if($tbIndex > 0)
				{
					$keys[] = $bKey . ':' . $idxValue[$tbIndex];
				}
				else
				{
					foreach($idxValue as $i)
					{
						$keys[] = $bKey . ':' . $i;
					}
				}

				switch($cache->getType())
				{
					case 'redis':
						$values = $cache->getRefer()->mGet($keys);

						if(!empty(\Config\Define::CACHE_GAME_DATA_DB))
						{
							$cache->getRefer()->select(\Config\Define::CACHE_GAME_DATA_DB);
						}
						break;
					case 'memcache':
						$values = $cache->get($keys);
						break;
				}

				if(!empty($values)){
					$key = null;
					foreach($values as $num=>$value){
						if(empty($value)) continue;
						//$value = msgpack_unpack($value);
						$value = json_decode($value, true);
						if($tbIndex > 0)
						{
							$data = $value;
						}
						else
						{
							$data[] = $value;
						}
					}
				}
			}
		}

		\Library\BaseController::$gameData[$game_key] = $data;
		return $data;
	}
}
