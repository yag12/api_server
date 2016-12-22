<?php
/**
* @Desc controller
*/
namespace Library;

abstract class BaseController extends \Library\Ab
{
	protected $controllerName = 'Index';
	protected $actionName = 'index';
	protected $params = array();
	protected $response = array();
	protected $resultCode = \Config\Result::SUCCESS;
	protected $accid = null;
	protected $guid = 0;
	protected $os = 0;
	protected $device = null;
	protected $platform = 0;
	protected $version = null;
	protected $serverInfo = array();
	protected $isPlayer = true;
	protected $isServer = true;

	static $player = array();
	static $chkServer = false;
	static $chkVersion = false;
	static $object = array();
	static $gameData = array();

	/**
	* @Desc controller startup
	*
	* @Param $controllerName
	* @Param $actionName
	* @Param $params
	* @Param $config
	* @Param $infos
	*
	* @Return mixed
	*/
	final public function startup($controllerName = 'Index', $actionName = 'index', $params = array(), $config = array(), $infos = array())
	{
		$this->controllerName = $controllerName;
		$this->actionName = !empty($actionName) ? $actionName : 'index';
		$this->params = &$params;
		$this->config = &$config;

		if(!empty($infos))
		{
			foreach($infos as $key=>$value)
			{
				$this->{$key} = $value;
			}
		}

		if($this->isServer === true)
		{
			if($this->isServerInfo() === false)
			{
				throw new \Exception(\Config\Result::MSG_CHECK_SERVER, \Config\Result::CHECK_SERVER);
				return false;
			}
			elseif($this->isVersion() === false)
			{
				throw new \Exception(\Config\Result::MSG_CHECK_VERSION, \Config\Result::CHECK_VERSION);
				return false;
			}
		}

		if($this->isPlayer === true && empty($this->guid))
		{
			throw new \Exception(\Config\Result::MSG_ERROR_NOT_FIND_GUID, \Config\Result::ERROR_NOT_FIND_GUID);
			return false;
		}

		if(is_callable(array($this, $actionName), false) === true)
		{
			// is public
			$reflector = new \ReflectionClass($this);
			$methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
			foreach($methods as $method)
			{
				if($method->name == $actionName)
				{
					$this->init();

					$result = call_user_func(array($this, $actionName));
					if($result !== false) return $this->output();
					break;
				}
			}
		}

		throw new \Exception(\Config\Result::MSG_ERROR_ACTION . $controllerName . "::" . $actionName, \Config\Result::ERROR_ACTION);
		return false;
	}

	/**
	* @Desc set response
	*
	* @Param $method
	* @Param $response
	*
	* @Return void
	*/
	final protected function setResponse($method = null, $response = array())
	{
		$this->response[$method] = $response;
	}

	/**
	* @Desc output json data
	*
	* @Param boolean $response
	*
	* @Return mixed
	*/
	final protected function output()
	{
		return $this->response;
	}

	/**
	* @Desc initiated
	*
	* @Param void
	*
	* @Return void
	*/
	protected function init(){ }

	/**
	* @Desc get result code
	*
	* @Return mixed
	*/
	final public function getResultCode()
	{
		return $this->resultCode;
	}

	/**
	* @Desc set result code
	*
	* @Param $resultCode
	*
	* @Return void
	*/
	final public function setResultCode($resultCode)
	{
		$resultCode = !empty($resultCode) ? $resultCode : \Config\Result::SUCCESS;
		$this->resultCode = $resultCode;
	}

	/**
	* @Desc server info checking
	*
	* @Return boolean
	*/
	final protected function isServerInfo()
	{
		if(\Library\BaseController::$chkServer === true) return true;

		$boolServer = false;
		$cache = $this->getCache('server');
		if(!empty($cache))
		{
			$redis = $cache->getRefer();
			$redis->select(\Config\Define::CACHE_SERVER_DB);
			//$this->serverInfo = $redis->hGetAll(\Config\Define::CACHE_SERVER_KEY);
			$this->serverInfo = array(
				'aos' => $redis->hget(\Config\Define::CACHE_SERVER_KEY, 'aos'),
				'ios' => $redis->hget(\Config\Define::CACHE_SERVER_KEY, 'ios'),
				'version' => $redis->hget(\Config\Define::CACHE_SERVER_KEY, 'version'),
			);

			switch($this->os)
			{
				case 1: // aos
					$boolServer = !empty($this->serverInfo['aos']) ? true : false;
					break;
				case 2: // ios
					$boolServer = !empty($this->serverInfo['ios']) ? true : false;
					break;
			}

			if($boolServer === false)
			{
				// 유지보수 중 접근 가능한 계정
				if(!empty($this->serverInfo['users']))
				{
					$users = json_decode($this->serverInfo['users'], true);
					if(array_search($this->accid, $users) !== false) $boolServer = true;
				}
			}

			\Library\BaseController::$chkServer = true;
		}

		return $boolServer;
	}

	/**
	* @Desc version checking
	*
	* @Param $version
	*
	* @Return boolean
	*/
	final protected function isVersion()
	{
		if(\Library\BaseController::$chkVersion === true) return true;

		$boolVersion = !empty($this->serverInfo['version']) ? true : false;
		if($boolVersion === true)
		{
			$boolVersion =  ($this->version == $this->serverInfo['version']) ? true : false;
		}

		\Library\BaseController::$chkVersion = true;

		return $boolVersion;
	}

	/**
	* @Desc log write
	*
	* @Param mixed $name
	* @Param array $logs
	*
	* @Return void
	*/
	final protected function logs($name = 'index', $logs = array())
	{
		\Library\Dispatcher::logs($name, $logs, array(
			'guid' => $this->guid,
			'os' => $this->os,
			'device' => $this->device,
			'platform' => $this->platform,
			'version' => $this->version,
			'url' => $this->config->log_url
		));
	}
}
