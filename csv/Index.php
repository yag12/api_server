<?php
/**
* @Desc 
*
*/
namespace Csv;

class Index extends \Library\BaseLog
{
	public $test0;
	public $test1;
	public $test2;

	protected $_name = 'log_test_index';
	protected $_req = array(
		array('test0', 'string', 't'),
		array('test1', 'array', array(0)),
		array('test2', 'array', array(0)),
	);
}
