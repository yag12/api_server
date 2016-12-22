<?php
/**
* @Desc error code
*
*/
namespace Config;

final class Result
{
	// 성공
	const SUCCESS = 0;
	// 서버 에러
	const FAILURE = 500;
	// 파라미터 에러(필수 파라미터 체크)
	const ERROR_PARAMENTS = 100001;
	// 시크릿키 에러
	const ERROR_SECRETKEY = 100002;
	// 메소드가 없는 경우
	const ERROR_METHOD = 100003;
	// 컨트롤러가 없는 경우
	const ERROR_CONTROLLER = 100004;
	// 서버 체크(유지보수)
	const CHECK_SERVER = 100011;
	// 버전 체크
	const CHECK_VERSION = 100012;
	// 엑션함수가 없는 경우
	const ERROR_ACTION = 100013;
	// 데이터를 캐시에 등록 실패
	const ERROR_CACHE_SAVE = 100014;
	// 게임 데이터 가져오기 실패
	const ERROR_GET_GAME_DATA = 100015;
	// 게임 ID가 없음
	const ERROR_NOT_FIND_GUID = 100016;
	// DB 접속 실패
	const ERROR_NOT_DB_CONNECTED = 100017;
	// 파일이 없습니다.
	const ERROR_FILE_NOT_FIND = 100018;
	// 로그 파일 쓰기 권한 에러
	const ERROR_LOG_FILE_WRITABLE = 100019;

	// 계정 탈퇴 실패
	const CONTROLLER_ERROR000 = 100100;
	// 플레이어 이름 사용하지 못함.
	const CONTROLLER_ERROR001 = 100101;
	// 플레이어 정보 생성 실패
	const CONTROLLER_ERROR002 = 100102;
	// 플레이어 정보를 가져오기 실패.
	const CONTROLLER_ERROR003 = 100103;




	const MSG_ERROR_PARAMENTS = '필수 파라미터 에러';
	const MSG_ERROR_SECRETKEY = '시크릿키 에러';
	const MSG_ERROR_METHOD = '메소드 파라미터 에러';
	const MSG_ERROR_CONTROLLER = '존재하지 않는 컨트롤러 : ';
	const MSG_CHECK_SERVER = '유지보수 중';
	const MSG_CHECK_VERSION = '버전이 맞지 않음';
	const MSG_ERROR_ACTION = '존재하지 않는 액션 : ';
	const MSG_ERROR_CACHE_SAVE = '데이터를 캐시에 등록 실패';
	const MSG_ERROR_GET_GAME_DATA = '게임 데이터 가져오기 실패';
	const MSG_ERROR_NOT_FIND_GUID = '게임 ID가 없음';
	const MSG_ERROR_NOT_DB_CONNECTED = 'DB 접속 실패';
	const MSG_ERROR_FILE_NOT_FIND = '파일이 없습니다';
	const MSG_ERROR_LOG_FILE_WRITABLE = '로그 파일 쓰기 권한 에러';


	const MSG_CONTROLLER_ERROR000 = '계정 탈퇴 실패';
	const MSG_CONTROLLER_ERROR001 = '플레이어 이름 사용하지 못함.';
	const MSG_CONTROLLER_ERROR002 = '플레이어 정보 생성 실패';
	const MSG_CONTROLLER_ERROR003 = '플레이어 정보를 가져오기 실패.';
}
