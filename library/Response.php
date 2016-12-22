<?php
/**
* @Desc response
*
*/
namespace Library;

abstract class BaseResponse
{
	protected $_req = array();

	/**
	* @Desc construct
	* @Return void
	*/
	public function __construct($data = array())
	{
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
	* @Param void
	* @Return array
	*/
	public function convert(){
		$data = array();
		$data[] = $this->class_code;

		if(!empty($this->_req))
		{
			foreach($this->_req as $req)
			{
				if(empty($req[0])) continue;
				$param = $req[0];
				$type = !empty($req[1]) ? $req[1] : 'string';
				$default = !empty($req[2]) ? $req[2] : null;
				$value = !empty($this->{$param}) ? $this->{$param} : $default;
				$sub = !empty($req[3]) ? $req[3] : null;

				switch($type)
				{
					case 'string':
						$data[] = (string) $value;
						break;
					case 'int':
						$data[] = (int) $value;
						break;
					case 'double':
						$data[] = (double) $value;
						break;
					case 'array':
						$data[] = (array) $value;
						break;
					case 'object':
						$data[] = (array) $this->subResponse($sub, $value);
						break;
					default:
						$data[] = $value;
						break;
				}
			}
		}

		return $data;
	}

	/**
	* @Desc sub response data
	*
	* @Param $response
	*
	* @Return array
	*/
	final protected function subResponse($name = null, $response = array())
	{
		if(!empty($name) && !empty($response))
		{
			foreach($response as &$res)
			{
				$res = \Library\Dispatcher::getConvert($name, $res);
			}
		}

		return $response;
	}

	/**
	* @Desc get req
	*
	* @Return array
	*/
	public function getReq()
	{
		return $this->_req;
	}
}
