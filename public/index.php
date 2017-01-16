<?php
$http_origin = $_SERVER['HTTP_ORIGIN'];
if ($http_origin == "http://mailerf.epicenterlabs.com" || $http_origin == "http://mailerfe.epicenterlabs.com") { 
	    header("Access-Control-Allow-Origin: $http_origin");
}
header("Access-Control-Allow-Credentials: true");
chdir('../core');
require 'common.inc.php';
FrontController::run();
?>
