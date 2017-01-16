<?php
use SimpleMVC\HttpUtils;
use SimpleMVC\Log;
use Firebase\JWT\ExpiredException; 
class FrontController {

	public static  function run() {
		try {
			$ctrl = null;
			if (isset($_REQUEST['module']) && isset($_REQUEST['action'])) {
				$ctrl = self::getControllerFromModule();
			} 

			if($ctrl) {
				$ctrl->run();
			} else {
				HttpUtils::badRequest();
			}
		} catch (Exception $e) {
			if ($e instanceof AuthException OR $e instanceof ExpiredException) {
				$resp = array('error' => "auth_error ".$e->getMessage());
				echo json_encode($resp);
			} else {
				Log::error($e->getMessage());
				HttpUtils::internalError("hot hot server");
			}
		}
	}

	private static function getControllerFromModule() {
		$ctrl = null;
		$module = $_REQUEST['module'];
		switch($module) {
		case 'access':
			$ctrl = new AccessController();
			break;

		case 'mail':
			$ctrl = new MailController();
			break;
		}
		return $ctrl;
	}
}
