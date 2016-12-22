DELIMITER //

/**
 * 계정 생성 및 인증
 * @param [in] Accid
 * @param [in] Os
 * @param [in] Platform
 * @param [in] Device
 * @param [in] Version
 * @param [in] NormalcyNum
 * @param [in] PauseNum
 */
DROP PROCEDURE IF EXISTS `get_account` //
CREATE PROCEDURE `get_account`(
	IN `aAccId` VARCHAR(255),
	IN `aOs` INT,
	IN `aPlatform` INT,
	IN `aDevice` VARCHAR(100),
	IN `aVersion` VARCHAR(10),
	IN `aNormalcyNum` INT,
	IN `aPauseNum` INT
)
BEGIN
	declare ResultCode INT;
	declare IsCount INT;
	declare MaxGuid INT;

	set ResultCode = 0;
	set IsCount = 0;
	set MaxGuid = 0;

	-- 계정이 존재하는지 여부
	select count(*) into IsCount from `account` where `id` = aAccId;

	if IsCount = 0
	then
		-- 계정 생성
		select if(max(`guid`), max(`guid`) + 1, 1) into MaxGuid from `account`;
		insert into account (`id`, `guid`, `status`, `os`, `platform`, `device`, `version`, `create_date`, `reg_date`) values (aAccid, MaxGuid, aNormalcyNum, aOs, aPlatform, aDevice, aVersion, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
	else
		update `account` set `os` = aOs, `platform` = aPlatform, `device` = aDevice, `version` = aVersion, `reg_date` = UNIX_TIMESTAMP() where `id` = aAccId;
		-- 일시정지 기간이 완료된 계정
		update account set status = aNormalcyNum, cease_date = 0, msg = "" where id = aAccId and status = aPauseNum and cease_date <= UNIX_TIMESTAMP();
	end if;

	select IsCount as is_account, `account`.* from `account` where `id` = aAccId;
END;//


/**
 * 계정 복구
 * @param [in] Guid
 * @param [in] NormalcyNum
 * @param [in] RemoveNum
 */
DROP PROCEDURE IF EXISTS `normalcy_account` //
CREATE PROCEDURE `normalcy_account`(
	IN `aGuid` INT,
	IN `aNormalcyNum` INT,
	IN `aRemoveNum` INT
)
BEGIN
	declare ResultCode INT;
	declare IsCount INT;
	declare CheckId text;

	set ResultCode = 0;
	set IsCount = 0;

	-- 계정이 존재하는지 여부
	select count(*) into IsCount from `account` where `guid` = aGuid;

	if IsCount = 0
	then
		set ResultCode = -1;
	else
		-- 복구할 ID
		select if(`status` = aRemoveNum, SUBSTRING_INDEX(`id`, "_", 1), `id`) into CheckId from `account` where `guid` = aGuid;

		select count(*) into IsCount from `account` where id = CheckId;
	
		if IsCount = 0
		then
			update account set id = CheckId, status = aNormalcyNum, cease_date = 0, delete_date = 0, msg = "" where guid = aGuid;
		else
			-- 복구할 ID가 이미 존재하는 경우
			set ResultCode = -2;
		end if;
	end if;

	select ResultCode;
END;//



