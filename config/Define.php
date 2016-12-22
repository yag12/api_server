<?php
/**
* @Desc define
*
*/
namespace Config;

final class Define
{
	// 디버그용 시크릿 해쉬 
	const DEBUG_HASH = 'ASDDSGASDASDWTDFVXVXCVDSFWERWZFDSDFH';
	// 인증용 시크릿 해쉬 
	const HASH_SEED = 'DE18AE7DFE417D51791066DCE16FACBF';
	// 서버 인코딩 : utf8
	const SERVER_ENCODING = 'utf8';

	// 서버 운영
	const SERVER_INFO_RUN = 1;
	// 서버 유지보수 중
	const SERVER_INFO_REPAIR = 0;

	// 기본 캐시 서버네임
	const CACHE_DEFAULT_NAME = 'default';

	// 캐시 정보
	const CACHE_GAME_DATA_DB = 1;
	const CACHE_SERVER_KEY = 'server_info';
	const CACHE_SERVER_TTL = -1;
	const CACHE_SERVER_DB = 0;
	const CACHE_SESSION_KEY = 'session:game_id:';
	const CACHE_SESSION_TTL = 36800;

	// graphite statsd
	const STATSD_SERVER = 'udp://192.168.0.100';
	const STATSD_PORT = 8125;
	const STATSD_PREFIX = 'http.game_';

	// 로그 시간 간격
	const LOG_TIME_INTERVAL = 5;

	// 플레이어 기본 정보 존재
	const PLAYER_IS_ACCOUNT = 1;
	// 플레이어 기본 정보 존재 안함
	const PLAYER_IS_NOT_ACCOUNT = 0;
	// 플레이어 상태 : 정상
	const PLYAER_STATUS_NORMALCY = 0;
	// 플레이어 상태 : 일시정지
	const PLYAER_STATUS_PAUSE = 1;
	// 플레이어 상태 : 영구정지
	const PLYAER_STATUS_STOP = 2;
	// 플레이어 상태 : 삭제
	const PLYAER_STATUS_REMOVE = 9;
	// 플레이어 닉네임수
	const PLYAER_NICNAME_MIN = 2;
	const PLYAER_NICNAME_MAX = 10;
}
