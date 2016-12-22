<?php
/**
* @Desc 새계정 생성 로그
* 데이터 형식
* time,guid,os,device,flatform,version
*/
namespace Csv;

class NewAccount extends \Library\BaseLog
{
	protected $_name = 'log_account_new';
	protected $_req = array(
	);
}
