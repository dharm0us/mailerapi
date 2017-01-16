<?php

set_time_limit(0);
include 'autoload.inc.php';
date_default_timezone_set('Asia/Kolkata');
ini_set( 'default_charset', 'UTF-8' );
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

define('DEFAULT_LOG_LOCATION', '/var/log/mailer_logs/default_log');
define('ERROR_LOG_LOCATION', '/var/log/mailer_logs/error_log');
define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');
define('JWT_SECRET', '');
define('ENDPOINT', 'mailer-epic');



ini_set("error_log", ERROR_LOG_LOCATION);

function catchableErrorHandler($errno, $errstr, $errfile, $errline) {
	if (error_reporting() !== 0) {
		SimpleMVC\Log::error("REQUEST = ",$_REQUEST,"no = $errno str = $errstr file = $errfile line = $errline");
	}
}

set_error_handler('catchableErrorHandler');
?>
