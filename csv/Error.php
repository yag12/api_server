<?php
/**
* @Desc 에러 로그
* 데이터 형식
* time,guid,os,device,flatform,version,code,message,file,line
*/
namespace Csv;

class Error extends \Library\BaseLog
{
	public $code;
	public $message;
	public $file;
	public $line;

	protected $_name = 'log_error_exception';
	protected $_req = array(
		array('code', 'int', 0),
		array('message', 'string', ''),
		array('file', 'string', ''),
		array('line', 'int', 0),
	);
}
