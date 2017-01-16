<?php

spl_autoload_register(null, false);

// specify extensions that may be loaded 
spl_autoload_extensions('.php, .class.php');


function classLoader($class) {
	$filename = ($class) . '.php';
	$paths = array();
	$paths[] = 'classes/'.$filename;
	$paths[] = 'classes/service/'.$filename;
	$paths[] = 'classes/exception/'.$filename;
	$paths[] = 'classes/model/'.$filename;
	$paths[] = 'classes/controller/'.$filename;
	foreach($paths as $path) {
		if (file_exists($path)) {
			include_once $path;
			return true;
		}
	}
	return false;
}

function loader($class) {
	$filepath = ($class) . '.php';
	if (!file_exists($filepath)) {
		return false;
	}
	include_once $filepath;
}

spl_autoload_register('classLoader');
require 'vendor/autoload.php';
?>
