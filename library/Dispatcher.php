<?php
/**
* @Desc dispatcher
*
*/
namespace Library;

class Dispatcher
{
	protected $result = array('error' => \Config\Result::SUCCESS);
	protected $config = array();
	protected $guid = 0;
	protected $os = 0;
	protected $device = null;
	protected $version = null;
	protected $platform = 0;
	protected $convert = array();
	static $sessionId = 0;
	static $caches = array();
	static $player = array();

	/**
	* @Desc construct
	*
	* @Param $request
	*
	* @Return void
	*/
	public function __construct($request = array())
	{
		try
		{
			$this->config = new \Config\Info;

			if($this->isParams($request) === false)
			{
				throw new \Exception(\Config\Result::MSG_ERROR_PARAMENTS, \Config\Result::ERROR_PARAMENTS);
				return false;
			}

			if(!empty($request))
			{
				$this->accid = !empty($request['accid']) ? (string) $request['accid'] : null;
				$this->guid = !empty($request['guid']) ? (int) $request['guid'] : 0;
				$this->os = !empty($request['os']) ? (int) $request['os'] : 0;
				$this->device = !empty($request['device']) ? (string) $request['device'] : null;
				$this->version = !empty($request['version']) ? (string) $request['version'] : null;
				$this->platform = !empty($request['platform']) ? (int) $request['platform'] : 0;
				$clientHash = !empty($request['client_hash']) ? $request['client_hash'] : null;
				unset($request['client_hash']);
				if($this->authSecretKey($request, $clientHash) == false)
				{
					throw new \Exception(\Config\Result::MSG_ERROR_SECRETKEY, \Config\Result::ERROR_SECRETKEY);
					return false;
				}
				unset($request['accid']);
				unset($request['guid']);
				unset($request['os']);
				unset($request['device']);
				unset($request['version']);
				unset($request['platform']);

				$graphite_data = array();
				foreach($request as $method=>$params)
				{
					if(is_string($params))
					{
						$params = json_decode($params, true);
					}

					if(!is_array($params)) $params = array();
					foreach($params as $key=>$val)
					{
						$params[$key] = $this->escapeString($val);
					}
					$result = $this->init($method, $params);
					$graphite_data['api.' . $method] = '1|c';
					if($result === false) break;
				}

				if(!empty($graphite_data))
				{
					if(!empty($this->guid))
					{
						$cache = $this->config->cache_shard['statsd'];
						if(is_array($cache))
						{
							$cache = $cache[$this->guid%sizeof($cache)];
						}

						// 10초 기준으로 접속한 유저 수
						$cache = \Library\Dispatcher::cache($cache);
						if(!empty($cache))
						{
							$timer = $cache->getRefer();
							if($cache->getType() == 'redis')
							{
								$mktime = time();
								$sendTime = $timer->hget(\Config\Define::CACHE_STATSD_KEY, $this->guid);
								if(($mktime - $sendTime) > \Config\Define::CACHE_STATSD_TTL)
								{
									$graphite_data['guid'] = '1|c';
									$timer->hset(\Config\Define::CACHE_STATSD_KEY, $this->guid, $mktime);
								}
							}
						}
					}

					\Plugin\Graphite::multi($graphite_data);
				}
			}
		}
		catch(\Exception $e)
		{
			$code = $e->getCode();
			$this->result['error'] = !empty($code) ? $code : \Config\Result::FAILURE;
			\Library\Console::error($e);

			\Library\Dispatcher::logs('error', array(
				'code' => $code,
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine()
			), array(
				'guid' => $this->guid,
				'os' => $this->os,
				'device' => $this->device,
				'platform' => $this->platform,
				'version' => $this->version,
				'url' => $this->config->log_url
			));
		}
	}

	/**
	* @Desc startup
	*
	* @Return void
	*/
	static public function startup()
	{
		$startTime = microtime(true);
		\Library\Dispatcher::import(array(
			'config.Config',
			'config.Result',
			'config.Define',
			'library.Console',
			'library.Ab',
			'library.Controller',
			'plugin.Graphite',
		));

		$request = $_POST;
		$dispatcher = new Dispatcher($request);
		$dispatcher->result['server_time'] = time();
		$dispatcher->result['session_id'] = \Library\Dispatcher::$sessionId;

		$httpType = '';
		if(!empty($_SERVER['HTTP_ACCEPT']))
		{
			$httpAccept = explode(',', $_SERVER['HTTP_ACCEPT']);
			if($httpAccept[0] == 'application/json')
			{
				$httpType = $httpAccept[0];
				$dispatcher->displayJson();
			}
			else
			{
				$httpType = 'unity';
				$dispatcher->displayMsgPack();
			}
		}
		else
		{
			$dispatcher->displayMsgPack();
		}
			
		\Library\Console::debug(print_r(array(
			'request' => $request,
			'response' => $dispatcher->result,
			'convert' => $dispatcher->convert,
			'type' => $httpType,
			'use_time' => microtime(true) - $startTime
		), true));
	}

	/**
	* @Desc init
	*
	* @Param $method
	* @Param $params
	*
	* @Return boolean
	*/
	private function init($method = null, $params = array())
	{
		try
		{
			if(empty($method))
			{
				throw new \Exception(\Config\Result::MSG_ERROR_METHOD, \Config\Result::ERROR_METHOD);
				return false;
			}

			$split_method = explode('_', $method);
			$controllerName = $split_method[0];
			$actionName = substr($method, strlen($controllerName)+1);
			$actionName = !empty($actionName) ? $actionName : 'index';
			$controllerName = ucfirst($controllerName);

			$importName = 'controller.' . $controllerName;
			$controller = '\\Controller\\' . $controllerName . 'Controller';

			if(\Library\Dispatcher::import($importName))
			{
				if(class_exists($controller))
				{
					$infos = array(
						'accid' => $this->accid,
						'guid' => $this->guid,
						'os' => $this->os,
						'device' => $this->device,
						'platform' => $this->platform,
						'version' => $this->version
					);

					$obj = new $controller;
					$result = $obj->startup($controllerName, $actionName, $params, $this->config, $infos);
					if($result !== false) $this->result = array_merge($result, $this->result);
					if(!empty($resultCode = $obj->getResultCode()) && $this->result['error'] == \Config\Result::SUCCESS)
					{
						$this->result['error'] = $resultCode;
					}
				}
			}
			else
			{
				throw new \Exception(\Config\Result::MSG_ERROR_CONTROLLER . $controllerName, \Config\Result::ERROR_CONTROLLER);
				return false;
			}
		}
		catch(\Exception $e)
		{
			throw $e;
			return false;
		}

		return true;
	}

	/**
	* @Desc paraments check
	*
	* @Param $request
	*
	* @Return boolean
	*/
	private function isParams($request = array())
	{
		if(!isset($request['guid'])) return false;
		if(!isset($request['os'])) return false;
		if($request['client_hash'] !== \Config\Define::DEBUG_HASH)
		{
			if(!isset($request['version'])) return false;
			if(!isset($request['device'])) return false;
		}

		return true;
	}

	/**
	* @Desc paraments escape
	*
	* @Param $str
	*
	* @Return mixed
	*/
	private function escapeString($str = null)
	{
		if(is_string($str))
		{
			$source[] = '/\\\/';
			$target[] = '\\\\\\';
			$source[] = "/'/";
			$target[] = "\'";
			$str = preg_replace($source, $target, $str);
		}
		elseif(is_array($str))
		{
			foreach($str as $key=>$val)
			{
				$str[$key] = $this->escapeString($val);
			}
		}

		return $str;
	}

	/**
	* @Desc auth
	*
	* @Param $request
	* @Param $clientHash
	*
	* @Return 
	*/
	private function authSecretKey($request = array(), $clientHash = null)
	{
		if($clientHash == \Config\Define::DEBUG_HASH) return true;

		$request['session_id'] = 0;
		if($this->guid > 0)
		{
			$cache = $this->config->cache_shard['session'];
			if(is_array($cache))
			{
				$cache = $cache[$this->guid%sizeof($cache)];
			}

			$cache = \Library\Dispatcher::cache($cache);
			if(!empty($cache))
			{
				$sessionId = $cache->get(\Config\Define::CACHE_SESSION_KEY . $this->guid);
				if(empty($sessionId)) return false;

				$request['session_id'] = $sessionId;

				\Library\Dispatcher::$sessionId = uniqid();
				$option = null;
				if(!empty(\Config\Define::CACHE_SESSION_TTL))
				{
					switch($cache->getType())
					{
						case 'redis':
							// 데이터 유지시간 설정 및 유지시간 동안 데이터 변경 불가
							// $option = array('nx', 'ex' => \Config\Define::CACHE_SESSION_TTL);
							// 데이터 유지시간 설정
							$option = array('ex' => \Config\Define::CACHE_SESSION_TTL);
							break;
						case 'memcache':
							$option = \Config\Define::CACHE_SESSION_TTL;
							break;
					}
				}
				$cache->set(\Config\Define::CACHE_SESSION_KEY . $this->guid, \Library\Dispatcher::$sessionId, $option);
			}
		}

		$serverHash = md5(urldecode(http_build_query($request)) . \Config\Define::HASH_SEED);
		return (($clientHash !== null && $serverHash !== null) && $clientHash === $serverHash);
	}

	/**
	* @Desc get convert data
	*
	* @Param $name
	* @Param $value
	*
	* @Return array
	*/
	static function getConvert($name = null, $value = array())
	{
		$data = array();
		if(!is_null($name) && is_string($name))
		{
			$name = ucfirst($name);
			if(\Library\Dispatcher::import('response.' . $name))
			{
				$resName = '\\Response\\' . $name;
				$res = new $resName($value);
				$data = $res->convert();
			}
		}

		return $data;
	}

	/**
	* @Desc convert data
	*
	* @Return array
	*/
	private function convertResult()
	{
		$this->convert = array();
		if(\Library\Dispatcher::import('library.Response'))
		{
			if(\Library\Dispatcher::import('response.Result'))
			{
				$res = new \Response\Result($this->result);
				$this->convert[] = $res->convert();
			}

			foreach($this->result as $resName=>$resValue)
			{
				if(is_array($resValue) === false) continue;
				$convertData = \Library\Dispatcher::getConvert($resName, $resValue);
				if(!empty($convertData))
				{
					$this->convert[] = $convertData;
				}
			}
		}

		return $this->convert;
	}

	/**
	* @Desc msgpack
	*
	* @Return void
	*/
	private function displayMsgPack()
	{
		$data = \msgpack_pack($this->convertResult());
		$data = gzcompress($data, 9);

		$outdata = "";
		foreach(unpack("C*", $data) as $value)
		{
			$value = $value^0x9a;
			$outdata .= pack("C", $value);
		}

		header('HTTP/1.1 200');
		header('Status: 200');
		header('Content-Type: application/x-gzip');

		print($outdata);
	}

	/**
	* @Desc json
	*
	* @Return void
	*/
	private function displayJson()
	{
		$this->result['converts'] = $this->convertResult();
		header('Content-Type: application/json');
		print(json_encode($this->result));
	}

	/**
	* @Desc connect cache
	*
	* @Param $name
	*
	* @Return Cache
	*/
	static public function cache($name = null)
	{
		if(!empty(\Library\Dispatcher::$caches[$name]))
		{
			return \Library\Dispatcher::$caches[$name];
		}

		\Library\Dispatcher::$caches[$name] = null;
		if(!empty(\Config\Info::$cache))
		{
			if(!empty(\Config\Info::$cache[$name]))
			{
				$cacheConfig = \Config\Info::$cache[$name];
				switch($cacheConfig['type'])
				{
					case 'memcache':
						if(\Library\Dispatcher::import('library.Cache.InterfaceCache'))
						{
							if(\Library\Dispatcher::import('library.Cache.Memcache'))
							{
								\Library\Dispatcher::$caches[$name] = new \Library\Cache\MemsCache($cacheConfig);
							}
						}

						break;
					case 'redis':
						if(\Library\Dispatcher::import('library.Cache.InterfaceCache'))
						{
							if(\Library\Dispatcher::import('library.Cache.Redis'))
							{
								\Library\Dispatcher::$caches[$name] = new \Library\Cache\RedisCache($cacheConfig);
							}
						}

						break;
				}
			}
		}

		return \Library\Dispatcher::$caches[$name];
	}

	/**
	* @Desc file import
	*
	* @Param $name
	*
	* @Return boolean 
	*/
	static public function import($name = null)
	{
		if(empty($name)) return false;
		if(is_array($name))
		{
			foreach($name as $val)
			{
				$res = \Library\Dispatcher::import($val);
				if($res === false) return false;
			}

			return true;
		}

		$importType = null;
		$importFile = null;
		$split_import = explode('.', $name);
		foreach($split_import as $import)
		{
			if($importType === null)
			{
				$importType = $import;
			}
			else
			{
				$importFile = (!empty($importFile) ? $importFile . '/' : '') . $import;
			}
		}

		switch($importType)
		{
			case 'controller':
				$loadFile = CONTROLLER_DIR;
				break;
			case 'model':
				$loadFile = MODEL_DIR;
				break;
			case 'library':
				$loadFile = LIBRARY_DIR;
				break;
			case 'plugin':
				$loadFile = PLUGIN_DIR;
				break;
			case 'response':
				$loadFile = RESOURCE_DIR;
				break;
			case 'config':
				$loadFile = CONFIG_DIR;
				break;
			case 'csv':
				$loadFile = CSV_DIR;
				break;
		}

		$loadFile = $loadFile . $importFile . '.php';
		if(is_file($loadFile) === false)
		{
			throw new \Exception(\Config\Result::MSG_ERROR_FILE_NOT_FIND . ' :: ' . $loadFile, \Config\Result::ERROR_FILE_NOT_FIND);
			return false;
		}

		require_once $loadFile;

		return true;
	}

	/**
	* @Desc log wirte
	*
	* @Param $name
	* @Param $logs
	* @Param $extracts
	*
	* @Return void
	*/
	static public function logs($name = null, $logs = array(), $extracts = array())
	{
		if(\Library\Dispatcher::import('library.Log'))
		{
			$name = ucfirst($name);
			if(\Library\Dispatcher::import('csv.' . $name))
			{
				$guid = 0;
				$os = 0;
				$device = null;
				$platform = 0;
				$version = 0;
				$url = null;
				if(!empty($extracts))
				{
					extract($extracts, EXTR_PREFIX_SAME, 'log');
					$guid = !empty($log_guid) ? $log_guid : $guid;
					$os = !empty($log_os) ? $log_os : $os;
					$device = !empty($log_device) ? $log_device : $device;
					$platform = !empty($log_flatform) ? $log_flatform : $platform;
					$version = !empty($log_version) ? $log_version : $version;
					$log_url = !empty($log_url) ? $log_url : $url;
				}
				
				$defaults = array(
					time(), // 현재시간
					$guid, // 사용자 ID
					$os, // os
					$device, // 디바이스
					$platform, // 플랫폼
					$version, // 클라버전
				);

				$resName = '\\Csv\\' . $name;
				$res = new $resName($logs, $defaults);
				$log_type = defined('LOG_TYPE') ? LOG_TYPE : 0;
				$data = $res->convert($log_type);
				switch($log_type)
				{
					case 0: // file
						$res->toCsv();
						break;
					case 1: // mongoDb
						if($this->getPlugin('curl'))
						{
							$data['__DB__'] = $res->getDirectory();
							$data['__TB__'] = $res->getName();
							\Plugin\Curl::async_send($url, $data);
						}
						break;
				}
			}
		}
	}
}
