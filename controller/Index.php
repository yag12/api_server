<?php
/**
* @Desc
*
*/
namespace Controller;

class IndexController extends \Library\BaseController
{
	protected $isPlayer = false;
	protected $isServer = false;
    public $permission = 0;

	public function index()
	{
		$cache = $this->getCache('server');
		$redis = $cache->getRefer();
		$redis->select(\Config\Define::CACHE_SERVER_DB);
		//\Library\Console::debug('--------------------------------');

		//$server_info = $redis->hGetAll(\Config\Define::CACHE_SERVER_KEY);
		//$this->setResponse('serverInfo', $server_info);
		$this->setResponse('serverInfo', array(
			'aos' => $redis->hget(\Config\Define::CACHE_SERVER_KEY, 'aos'),
			'ios' => $redis->hget(\Config\Define::CACHE_SERVER_KEY, 'ios'),
			'version' => $redis->hget(\Config\Define::CACHE_SERVER_KEY, 'version'),
		));
	}

	/**
	* @Desc 데이터 초기화
	*
	* @Return 
	*/
	public function initialize()
	{
		// 캐시 초기화
		if(!empty(\Config\Info::$cache))
		{
			foreach(\Config\Info::$cache as $name=>$options)
			{
				$cache = \Library\Dispatcher::cache($name);
				switch($cache->getType())
				{
					case 'redis':
						$redis = $cache->getRefer();
						$redis->flushAll();
						break;
					case 'memcache':
						$memcache = $cache->getRefer();
						$memcache->flush();
						break;
				}
			}
		}

		// DB 초기화
		$sql = null;
		$tables = array(
			'account',
		);
		foreach($tables as $tb) $sql = $sql . 'TRUNCATE TABLE `' . $tb . '`;';
		foreach($this->config->db as $db)
		{
			switch($db['type'])
			{
				case 'mysqli':
					$mysql = new \mysqli($db['host'], $db['user'], $db['passwd'], $db['name'], $db['port']);
					$mysql->multi_query($sql);
					break;
			}
		}
	}

	/**
	* @Desc 서버 상태 변경
	*
	* @Return 
	*/
	public function serverInfo()
	{
		$info = $this->params['info'];
		
		$cache = $this->getCache('server');
		$redis = $cache->getRefer();
		$redis->select(\Config\Define::CACHE_SERVER_DB);
		
		if(!empty($info) && is_array($info))
		{
			foreach($info as $key=>$val)
			{
				$redis->hset(\Config\Define::CACHE_SERVER_KEY, $key, $val);
			}
		}
	}
}
