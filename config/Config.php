<?php
/**
* @Desc config
*
*/
namespace Config;

final class Info
{
	// DB 정보
	public $db = array(
		'db0' => array(
			'type' => 'mysqli',
			'host' => '0.0.0.0',
			'port' => 3306,
			'name' => 'api_server',
			'user' => 'api_user',
			'passwd' => 'api_user',
		),
		'db1' => array(
			'type' => 'mysqli',
			'host' => '0.0.0.0',
			'port' => 3306,
			'name' => 'api_server',
			'user' => 'api_user',
			'passwd' => 'api_user',
		),
	);

	// sharding DB
	public $db_shard = array(
		//'exchange' => array('db0'),
		//'hero' => array('db0', 'db1'),
	);

	// 캐시 정보
	static public $cache = array(
		//'memcache' => array(
		//	'type' => 'memcache',
		//	'servers' => array(
		//		array('0.0.0.0', 11211, 50),
		//		array('0.0.0.0', 11211, 50),
		//	),
		//	'prefix_key' => 'server_',
		//),
		'redis' => array(
			'type' => 'redis',
			'servers' => array('0.0.0.0', 6379),
			'prefix_key' => null,
		),
	);

	public $cache_shard = array(
		'default' => 'redis', // 기본 캐시
		'server' => 'redis', // 서버 상태 캐시
		'session' => 'redis', // 계정 데이터 캐시
	);
	
	// 로그 전송 URI
	public $log_url = '/logs.php';

	/**
	* @Desc construct
	*
	* @Return void
	*/
	public function __construct()
	{
		if(empty($this->db['default']))
		{
			$this->db['default'] = current($this->db);
		}

		$this->log_url = 'http://' . (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '0.0.0.0') . $this->log_url;
	}
}
