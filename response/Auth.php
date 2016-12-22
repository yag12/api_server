<?php
/**
* @Desc 
*
*/
namespace Response;

class Auth extends \Library\BaseResponse
{
	public $class_code = 3;
	public $guid;
	public $status;
	public $is_account;
	public $enddate;
	public $msg;

	protected $_req = array(
		array('guid', 'int', 0, null, '게임번호'),
		array('status', 'int', 0, null, '계정상태(0:정상, 1:일시중지, 2:영구정지, 9:삭제)'),
		array('is_account', 'int', 0, null, '0:새계정 생성, 1:계정 인증'),
		array('enddate', 'int', 0, null, '종료일자(일시중지 시)'),
		array('msg', 'string', '', null, '계정메시지(일지중지, 영구정지 시)'),
	);
}
