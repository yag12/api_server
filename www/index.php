<?php
// directory
define('ROOT', dirname(dirname(__FILE__)));
define('CONTROLLER_DIR', ROOT . '/controller/');
define('MODEL_DIR', ROOT . '/model/');
define('LIBRARY_DIR', ROOT . '/library/');
define('RESOURCE_DIR', ROOT . '/response/');
define('PLUGIN_DIR', ROOT . '/plugin/');
define('CONFIG_DIR', ROOT . '/config/');
define('CSV_DIR', ROOT . '/csv/');
define('LOG_DIR', ROOT . '/log/');
define('TBL_DIR', ROOT . '/data/tbl/');

// is error
define('IS_ERRORLOG', true);
// is debug
define('IS_DEBUGLOG', true);
// graphite send
define('IS_GRAPHITE', true);
// log type (0 = file, 1 = mongoDb)
define('LOG_TYPE', 0);
// game data type (0 = file, 1 = cache)
define('GAME_DATA_TYPE', 0);

// Server Timezone
date_default_timezone_set('Asia/Seoul');

require_once ROOT . '/library/Dispatcher.php';

\Library\Dispatcher::startup();
