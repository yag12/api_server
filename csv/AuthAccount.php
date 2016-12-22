<?php
/**
* @Desc 계정 인증 로그
* 데이터 형식
* time,guid,os,device,flatform,version,status,is_account
*/
namespace Csv;

class AuthAccount extends \Library\BaseLog
{
	public $status; // 계정 상태
	public $is_account; // 계정 생성여부

	protected $_name = 'log_account_auth';
	protected $_req = array(
		array('status', 'int', 0),
		array('is_account', 'int', 0),
	);
}
