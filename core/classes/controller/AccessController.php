<?php
use SimpleMVC\BaseController;
class AccessController extends BaseController {

	protected function setMaps() {
		$this->POST_Map['login'] = function() {
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			echo AccessService::login($user,$pass);
		};
	}
}

?>
