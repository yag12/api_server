<?php
/**
* @Desc result
* 에러코드 및 서버시간
*/
namespace Response;

class Result extends \Library\BaseResponse
{
	public $class_code = 1;
	public $error;
	public $server_time;
	public $session_id;

	// 변수명, 형타입, 기본값, 서브클래스명
	protected $_req = array(
		array('error', 'int', 0, null, '결과코드(0:정상처리, 500:서버에러)'),
		array('server_time', 'int', 0, null, '서버시간'),
		array('session_id', 'string', 0, null, '계정 세션ID')
	);
}
