<?php
/**
* @Desc 계정 탈퇴 로그
* 데이터 형식
* time,guid,os,device,flatform,version,msg
*/
namespace Csv;

class WithdrawAccount extends \Library\BaseLog
{
	public $msg; // 메시지

	protected $_name = 'log_account_withdraw';
	protected $_req = array(
		array('msg', 'string', ''),
	);
}
