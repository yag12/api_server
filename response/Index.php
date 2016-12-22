<?php
/**
* @Desc 
*
*/
namespace Response;

class Index extends \Library\BaseResponse
{
	public $class_code = 2;
	public $test;
	public $test1;
	public $test2;

	protected $_req = array(
		array('test', 'string', '', null),
		array('test1', 'array', '', null),
		array('test2', 'object', '', 'Result'),
	);
}
