<?

define('DEBUG_CONSTANTS', 0);

// Question mark is important!
define('GAME_SERVER', 'localhost');
define('GAME_SERVER_PORT', '1337');

define('GZIP_COMPRESS', 1);

define('WEB_DATA_VERSION', '00008');
define('WEB_FINGERPRINT', md5(WEB_DATA_VERSION));

define('STATIC_CACHE_TIME', 60 * 60 * 24 * 30 * 5);
?>