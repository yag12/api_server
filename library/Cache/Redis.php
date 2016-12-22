<?php
/**
* @Desc redis
*
*/
namespace Library\Cache;

class RedisCache implements \Library\Cache\InterfaceCache
{
	protected $redis;

	/**
	* @Desc construct
	* @Param array $config
	* @Return void
	*/
	public function __construct($config = array())
	{ 
		if(empty($config['servers'])) $config['servers'] = array('127.0.0.1', 6379, null);
		$this->connection($config);
	}

	/**
	* @Desc get cache type
	*
	* @Return string
	*/
	public function getType()
	{
		return 'redis';
	}

	/**
	* @Desc get reference
	*
	* @Return 
	*/
	public function getRefer()
	{
		return $this->redis;
	}

	/**
	* @Desc redis connect
	*
	*/
	public function connection($config = array())
	{
		$servers = !empty($config['servers']) ? $config['servers'] : array();
		$prefix_key = !empty($config['prefix_key']) ? $config['prefix_key'] : null;

		$this->redis = new \Redis;
		$this->redis->connect($servers[0], $servers[1]);

		if(!empty($servers[2]))
		{
			// redis 비밀번호
			$this->redis->auth($servers[2]);
		}

		if(!empty($prefix_key))
		{
			// redis 접두사
			$this->redis->setOption(\Redis::OPT_PREFIX, $prefix_key);
		}
	}

	/**
	* @Desc redis get
	*
	*/
	public function get($key = null)
	{
		return $this->redis->get($key);
	}

	/**
	* @Desc redis set
	*
	*/
	public function set()
	{
		$args_length = func_num_args();
		if ($args_length >= 2)
		{
			$args = func_get_args();
			$key = $args[0];
			$val = $args[1];
			$opt = !empty($args[2]) ? $args[2] : null;

			return $this->redis->set($key, $val, $opt);
		}
	}

	/**
	* @Desc redis delete keys pattern
	*
	* @Param $pattern
	*
	* @Return false or mixed
	*/
	public function delPatternKeys($pattern = null)
	{
		if($pattern === null) return false;

		$output = shell_exec('redis-cli keys "' . $pattern . '*" | xargs redis-cli DEL');
		return $output;
	}
}
