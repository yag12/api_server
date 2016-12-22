<?php
namespace Plugin;

/**
* @Desc Send Data to Graphite
*/
class Graphite
{
	public static $server = 'udp://0.0.0.0';
	public static $port = 8125;
	public static $prefix = 'http.game_';

	public static function init()
	{
		$server = constant('\Config\Define::STATSD_SERVER');
		$port = constant('\Config\Define::STATSD_PORT');
		$prefix = constant('\Config\Define::STATSD_PREFIX');

		if(!empty($server)) \Plugin\Graphite::$server = $server;
		if(!empty($port)) \Plugin\Graphite::$port = $port;
		if(!empty($prefix)) \Plugin\Graphite::$prefix = $prefix;
	}
	
	/**
	* @Desc Api key
	*
	* @Param mixed $apikey
	*
	* @Return string
	*/
	public static function getApiKey($apikey = 'user')
	{
		return \Plugin\Graphite::$prefix . $apikey;
	}

	/**
	* @Desc multi send
	*
	* @Param $values
	*
	* @Return void
	*/
	public static function multi($values = array())
	{
		\Plugin\Graphite::init();
		if(function_exists('fsockopen') && IS_GRAPHITE === true)
		{
			if(is_array($values))
			{
				$server = \Plugin\Graphite::$server;
				$port = \Plugin\Graphite::$port;
				$line = null;
				foreach($values as $key=>$val)
				{
					$line = $line . "\n" . \Plugin\Graphite::getApiKey($key) . ":" . $val;
				}

				$fp = fsockopen($server, $port, $err, $errc, 1);
				if($fp)
				{
					fwrite($fp, $line);
					fclose($fp);
				}
			}
		}
	}

	/**
	* @Desc Send data
	*
	* @Param mixed $apikey
	* @Param int $value
	* @Param mixed $func
	*
	* @Return void
	*/
	public static function send($apikey = 'user', $value = 1, $func = 'c')
	{
		\Plugin\Graphite::init();
		if(function_exists('fsockopen') && IS_GRAPHITE === true)
		{
			$server = \Plugin\Graphite::$server;
			$port = \Plugin\Graphite::$port;
			$line = \Plugin\Graphite::getApiKey($apikey) . ':' . $value . '|' . $func;
		
			$fp = fsockopen($server, $port, $err, $errc, 1);
			if($fp)
			{
				fwrite($fp, $line);
				fclose($fp);
			}
		}
	}
}
