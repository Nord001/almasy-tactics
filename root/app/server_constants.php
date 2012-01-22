<?

define('DEBUG_CONSTANTS', 1);

// Question mark is important!
define('GAME_SERVER', 'shantou.zapto.org');
define('GAME_SERVER_PORT', '1338');

define('GZIP_COMPRESS', 1);

define('WEB_DATA_VERSION', '00004');
define('WEB_FINGERPRINT', md5(WEB_DATA_VERSION));

define('STATIC_CACHE_TIME', 60 * 60 * 24 * 30 * 5);

?>