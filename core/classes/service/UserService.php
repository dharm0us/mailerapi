<?php
use SimpleMVC\DBP;
class UserService {
	public static function getUserFromName($name) {
		$query = "select * from users where name = :name";
		$bindings = array('name' => $name);
		$user = DBP::getObject($query,$bindings,'User');
		return $user;
	}
}
