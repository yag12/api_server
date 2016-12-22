<?php
/**
* @Desc server
* 
*/
namespace Response;

class ServerInfo extends \Library\BaseResponse
{
	public $class_code = 4;
	public $aos;
	public $ios;
	public $version;

	// 변수명, 형타입, 기본값, 서브클래스명
	protected $_req = array(
		array('aos', 'int', 0, null, 'AOS'),
		array('ios', 'int', 0, null, 'IOS'),
		array('version', 'string', 0, null, '버젼')
	);
}
