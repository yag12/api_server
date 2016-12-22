<?php
/**
* @Desc
*
*/
namespace Controller;

class AuthController extends \Library\BaseController
{
	protected $isPlayer = false;

	protected function init()
	{
		$this->authModel = $this->getModel('auth');
	}

	/**
	* @Desc 계정 인증
	*
	* @Return 
	*/
	public function index()
	{
		$acc_id = $this->accid;

		$account = $this->authModel->getAccount($acc_id, $this->os, $this->platform, $this->device, $this->version);

		$this->guid = $guid = $account['guid'];
		$is_account = $account['is_account'];
		$status = $account['status'];
		$enddate = $account['cease_date'];
		$msg = $account['msg'];
		if($guid > 0 && $status == \Config\Define::PLYAER_STATUS_NORMALCY)
		{
			$cache = $this->getCache('session');
			if(!empty($cache))
			{
				\Library\Dispatcher::$sessionId = uniqid();
				$option = array();
				if(!empty(\Config\Define::CACHE_SESSION_TTL))
				{
					switch($cache->getType())
					{
						case 'redis':
							// 데이터 유지시간 설정 및 유지시간 동안 데이터 변경 불가
							// $option = array('nx', 'ex' => \Config\Define::CACHE_SESSION_TTL);
							// 데이터 유지시간 설정
							$option = array('ex' => \Config\Define::CACHE_SESSION_TTL);
							break;
						case 'memcache':
							$option = \Config\Define::CACHE_SESSION_TTL;
							break;
					}
				}
				$cache->set(\Config\Define::CACHE_SESSION_KEY . $guid, \Library\Dispatcher::$sessionId, $option);
			}
		}

		$this->setResponse('auth', array(
			'guid' => $guid,
			'status' => $status,
			'is_account' => $is_account,
			'enddate' => $enddate,
			'msg' => $msg
		));

		// TODO 새계정 생성 로그
		if($account['is_account'] == \Config\Define::PLAYER_IS_NOT_ACCOUNT) $this->logs('newAccount');
		
		// TODO 계정 인증 로그
		$this->logs('authAccount', array(
			'status' => $status,
			'is_account' => $is_account
		));
	}

	/**
	* @Desc 계정 삭제(탈퇴)
	*
	* @Return 
	*/
	public function withdraw()
	{
		$acc_id = $this->accid;
		$msg = !empty($this->params['msg']) ? $this->params['msg'] : '';

		if(($account = $this->authModel->withdrawAccount($acc_id)) === false)
		{
			throw new \Exception(\Config\Result::MSG_CONTROLLER_ERROR000, \Config\Result::CONTROLLER_ERROR000);
			return false;
		}

		$guid = $account['guid'];
		if($guid > 0)
		{
			$cache = $this->getCache('session');
			if(!empty($cache))
			{
				$redis = $cache->getRefer();
				// 세션 정보 삭제
				$redis->delete(\Config\Define::CACHE_SESSION_KEY . $guid);
				\Library\Dispatcher::$sessionId = 0;
			}
		}

		$this->setResponse('auth', array(
			'guid' => $guid,
			'status' => \Config\Define::PLYAER_STATUS_REMOVE,
			'is_account' => \Config\Define::PLAYER_IS_ACCOUNT
		));
		
		// TODO 계정 삭제 로그
		$this->logs('withdrawAccount', array(
			'msg' => $msg
		));
	}
}
