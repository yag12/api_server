<?php
/**
* @Desc curl
*/

namespace Plugin;

class Curl
{

	/**
	* @Desc curl send
	*
	* @Param string $url
	* @Param array $fields
	*
	* @Return mixed
	*/
	static public function send($url = null, $fields = array())
	{
		$defaults = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_POSTFIELDS => http_build_query($fields)
		);

		$ch = curl_init();
		curl_setopt_array($ch, $defaults);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	* @Desc curl async
	*
	* @Param string $url
	* @Param array $fields
	*
	* @Return void
	*/
	static public function async_send($url = null, $fields = array())
	{
		$post_string = http_build_query($fields);
		$parts = parse_url($url);
		$host = !empty($parts['host']) ? $parts['host'] : '0.0.0.0';
		$port = !empty($parts['port']) ? $parts['port'] : 80;
		$path = !empty($parts['path']) ? $parts['path'] : '';

		$fp = fsockopen($host, $port, $errno, $errstr, 30);
		$out = "POST " . $path . " HTTP/1.1\r\n";
		$out.= "Host: " . $host . "\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: " . strlen($post_string) . "\r\n";
		$out.= "Connection: Close\r\n\r\n";
		if (isset($post_string)) $out .= $post_string;

		fwrite($fp, $out);
		fclose($fp);
	}
}
