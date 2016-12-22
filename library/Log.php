<?php
/**
* @Desc log
*
*/
namespace Library;

abstract class BaseLog
{
	protected $_req = array();
	protected $_data = array();
	protected $_name = 'log_error';

	/**
	* @Desc 로그 파일명
	*
	* @Param $ext
	*
	* @Return string
	*/
	protected function getFileName($ext = 'csv')
	{
		$ip = !empty($_SERVER['SERVER_ADDR']) ? str_replace('.', '_', $_SERVER['SERVER_ADDR']) : ''; 
		$min = date('i');
		$min_part_int = floor($min / \Config\Define::LOG_TIME_INTERVAL) * \Config\Define::LOG_TIME_INTERVAL;
		$min_part = sprintf("%02d", $min_part_int);
		$now_date = date('YmdH') . $min_part;

		return $ip . '_' . $now_date . '_' . $this->_name . '.' . $ext;
	}

	/**
	* @Desc 로그 디렉토리 체크
	*
	* @Param $dir
	*
	* @Return boolean
	*/
	protected function isDir($dir = null)
	{
		if(is_dir($dir) === false)
		{
			if(mkdir($dir) === false)
			{
				throw new \Exception(\Config\Result::MSG_ERROR_LOG_FILE_WRITABLE, \Config\Result::ERROR_LOG_FILE_WRITABLE);
				return false;
			}
		}

		return true;
	}

	/**
	* @Desc get directory
	*
	* @Return mixed
	*/
	public function getDirectory()
	{
		$names = explode('_', $this->_name);
		return $names[1];
	}

	/**
	* @Desc get log name
	*
	* @Return mixed
	*/
	public function getName()
	{
		return $this->_name;
	}

	/**
	* @Desc construct
	*
	* @Param $data
	* @Param $defaults
	*
	* @Return 
	*/
	public function __construct($data = array(), $defaults = array())
	{
		$this->_data = array();
		if(is_array($defaults))
		{
			foreach($defaults as $value)
			{
				$this->_data[] = $value;
			}
		}

		if(is_array($data))
		{                                     
			foreach($data as $key=>$value)
			{
				$this->{$key} = $value;
			}
		}                                     
	}

	/**
	* @Desc destruct
	* @Return void
	*/
	public function __destruct(){ }

	/**
	* @Desc data convert
	*
	* @Param $log_type
	*
	* @Return 
	*/
	public function convert($log_type = 0){
		if(!empty($this->_req))
		{
			foreach($this->_req as $req)
			{
				if(empty($req[0])) continue;
				$param = $req[0];
				$type = !empty($req[1]) ? $req[1] : 'string';
				$default = !empty($req[2]) ? $req[2] : null;
				$value = !empty($this->{$param}) ? $this->{$param} : $default;

				switch($type)
				{
					case 'string':
						$this->_data[] = (string) $value;
						break;
					case 'int':
						$this->_data[] = (int) $value;
						break;
					case 'double':
						$this->_data[] = (double) $value;
						break;
					case 'array':
						switch($log_type)
						{
							case 0:
								$this->_data[] = implode(';', $value);
								break;
							case 1:
								$this->_data[] = (array) $value;
								break;
						}
						break;
					default:
						$this->_data[] = $value;
						break;
				}
			}
		}

		return $this->_data;
	}

	/**
	* @Desc csv 파일로 저장
	*
	* @Return boolean
	*/
	public function toCsv()
	{
		return $this->toFile();
	}

	/**
	* @Desc 로그 파일 저장
	*
	* @Param $ext
	* @Param $divide
	*
	* @Return boolean
	*/
	public function toFile($ext = 'csv', $divide = ',')
	{
		if(empty($this->_data)) return false;
		$data = implode($divide, $this->_data);

		$log_file = LOG_DIR . $ext;
		if($this->isDir($log_file) === false)
		{
			return false;
		}

		$log_file = $log_file . '/' . $this->getDirectory();
		if($this->isDir($log_file) === false)
		{
			return false;
		}

		$log_file = $log_file . '/' . $this->getFileName($ext);
		$resource = fopen($log_file, 'a+');
		if(!empty($resource))
		{
			fwrite($resource, $data."\r\n");
			fclose($resource);
		
			return true;
		}

		return false;
	}
}
