<?php
/**
* @Desc curl
*/

namespace Plugin;

class NgWordText
{
	public $ng_word;
	public $is_word_check;
	public function __construct($ang_word, $ais_word_check)
	{
		$this->ng_word = $ang_word;
		$this->is_word_check = $ais_word_check;
	}
}

class Ngword
{
	protected $data = array();
	protected $is_extension = true;

	public function __construct()
	{
		if(extension_loaded('ngword') === false)
		{
			$this->is_extension = false;
		}
	}

	/**
	* @Desc 금칙어 가져오기
	*
	* @Return 
	*/
	public function get()
	{
		if(empty($this->data))
		{
			$ngwords = file_get_contents(CONFIG_DIR . 'ngword.txt');
			$texts = explode("\n", $ngwords);
			foreach($texts as $text)
			{
				if(empty($text)) continue;
				list($word, $check) = explode("\t", $text);
				if($this->is_extension === true)
				{
					$this->data[] = new NgWordText($word, $check);
				}
				else
				{
					$this->data[] = $word;
				}
			}
		}

		return $this->data;
	}

	/**
	* @Desc 금칙어가 존재하는지 체크
	*
	* @Param $str
	*
	* @Return boolean
	*/
	public function check($str = null)
	{
		if($this->is_extension === true)
		{
			NgWordInit($this->get());
			$std = NgWordCheck($str);
			NgWordFinal();

			if(count($std->ngwords) > 0)
			{
				\Library\Console::debug(__METHOD__.":".__LINE__." ::: ".$str);
				return false;
			}
		}
		else
		{
			$patterns = implode("|", $this->get());
			if(strpos($patterns, $str) !== false)
			{
				\Library\Console::debug(__METHOD__.":".__LINE__." ::: ".$str);
				return false;
			}
		}

		return true;
	}

	/**
	* @Desc 금칙어 미출력
	*
	* @Param $str
	*
	* @Return string
	*/
	public function convert($str = null)
	{
		if($this->is_extension === true)
		{
			NgWordInit($this->get());
			$std = NgWordCheck($str);
			NgWordFinal();

			return $std->str;
		}
		else
		{
			$patterns = $this->get();
			return str_replace($patterns, "**", $str);
		}
	}
}
