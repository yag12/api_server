<?php
/**
* @Desc console log
*
*/

namespace Library;

class Console
{
	/**
	* @Desc error message
	*
	* @Param $e
	*
	* @Return void
	*/
	static public function error($e = null)
	{
		if(IS_ERRORLOG === true)
		{
			$NowDate = date("Ymd");
			$NowTime = date("H:i:s");

			$error_msg = array(
				'message' => $e->getMessage(),
				'file' => $e->getFile() . ' [' . $e->getLine() . ']',
				//'trace' => $e->getTrace()
			);

			$OpenFile = LOG_DIR . $NowDate.'_error.log';
			$WorkResource = fopen($OpenFile , "a");
			fwrite($WorkResource, '['.$NowTime.'] '.print_r($error_msg, true)."\r\n");
			fclose($WorkResource);
			//chmod($OpenFile, 0777);
		}
	}

	/**
	* @Desc console log
	*
	* @Param $val
	*
	* @Return void
	*/
	static public function debug($val = null)
	{
		if(IS_DEBUGLOG === true)
		{
			$NowDate = date("Ymd");
			$NowTime = date("H:i:s");

			$OpenFile = LOG_DIR . $NowDate.'_debug.log';
			$WorkResource = fopen($OpenFile , "a");
			fwrite($WorkResource, '['.$NowTime.'] '.$val."\r\n");
			fclose($WorkResource);
			//chmod($OpenFile, 0777);
		}
	}
}
