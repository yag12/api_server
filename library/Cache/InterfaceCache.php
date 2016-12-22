<?php 
/**
* @Desc interface cache
*
*/
namespace Library\Cache;

interface InterfaceCache
{
	public function getType();
	public function getRefer();
	public function connection();
	public function get();
	public function set();
}
