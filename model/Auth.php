<?php
/**
* @Desc
*
*/
namespace Model;

class AuthModel extends \Library\BaseModel
{
	/**
	* @Desc 계정정보 생성 및 가져오기
	*
	* @Param $acc_id
	* @Param $os
	* @Param $platform
	* @Param $device
	* @Param $version
	*
	* @Return array
	*/
	public function getAccount($acc_id = null, $os = 0, $platform = 0, $device = null, $version = null)
	{
		$sql = 'call get_account("' . $acc_id . '", ' . $os . ', ' . $platform . ', "' . $device . '", "' . $version . '", ' . \Config\Define::PLYAER_STATUS_NORMALCY . ', ' . \Config\Define::PLYAER_STATUS_PAUSE . ');';
		$result = $this->query($sql);
		$account = $result[0]['rows'][0];

		return $account;
	}

	/**
	* @Desc 클라이언트 고유ID 가져오기
	*
	* @Param $guid
	*
	* @Return mixed
	*/
	public function getAccId($guid = 0)
	{
		$sql = 'select id from account where guid = ' . $guid . ';';
		$result = $this->query($sql);

		return !empty($result[0]['rows'][0]['id']) ? $result[0]['rows'][0]['id'] : null;
	}

	/**
	* @Desc 계정 탈퇴
	*
	* @Param $acc_id
	*
	* @Return array
	*/
	public function withdrawAccount($acc_id = null)
	{
		$id = $acc_id . '_' . time();
		$sql = 'update account set id = "' . $id . '", status = ' . \Config\Define::PLYAER_STATUS_REMOVE . ', delete_date = UNIX_TIMESTAMP() where id = "' . $acc_id . '";';
		$sql = $sql . 'select status from account where id = "' . $id . '";';
		$result = $this->query($sql);

		return ($result[1]['rows'][0]['status'] == \Config\Define::PLYAER_STATUS_REMOVE) ? \Config\Define::PLYAER_STATUS_REMOVE : false;
	}


	/**
	* @Desc 정상계정
	*
	* @Param $guid
	*
	* @Return boolean
	*/
	public function normalcyStatus($guid = 0)
	{
		$sql = 'call normalcy_account(' . $guid . ', ' . \Config\Define::PLYAER_STATUS_NORMALCY . ', ' . \Config\Define::PLYAER_STATUS_REMOVE . ');';
		$result = $this->query($sql);

		return isset($result[0]['rows'][0]['ResultCode']) ? $result[0]['rows'][0]['ResultCode'] : -3;
	}

	/**
	* @Desc 계정 일시정지
	*
	* @Param $guid
	* @Param $cease_date
	* @Param $message
	*
	* @Return boolean
	*/
	public function pauseStatus($guid = 0, $cease_date = 0, $message = null)
	{
		$sql = 'update account set status = ' . \Config\Define::PLYAER_STATUS_PAUSE . ', cease_date = ' . $cease_date . ', msg = "' . $message . '" where guid = ' . $guid . ';';
		return $this->query($sql);
	}

	/**
	* @Desc 계정 영구정지
	*
	* @Param $guid
	* @Param $message
	*
	* @Return boolean
	*/
	public function stopStatus($guid = 0, $message = null)
	{
		$sql = 'update account set status = ' . \Config\Define::PLYAER_STATUS_STOP . ', cease_date = UNIX_TIMESTAMP(), msg = "' . $message . '" where guid = ' . $guid . ';';
		return $this->query($sql);
	}

}
