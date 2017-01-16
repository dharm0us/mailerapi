<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;
use SimpleMVC\DBP;

class AccessService {

	public static function login($userName,$password) {
		$resp = array();
		$query = "select * from users where name = :name and password = :password";
		$user = DBP::getObject($query, array('name' => $userName, 'password' => $password),'User');
		if($user) {
			$jwt = self::createToken($user->getId());
			$resp['token'] = $jwt; 
		} else {
			$resp['error'] = "Invalid credentials"; 
		}
		return json_encode($resp);
	}

	public static function ensureLogin() {
		self::verifyToken();
	}

	public static function getCurrentUser() {
		$token = $_REQUEST['access_token'];
		$key = JWT_SECRET; 
		$decoded = self::decodeToken(); 
		return $decoded->userId;
	}

	private static function decodeToken() {
		$jwt = $_REQUEST['access_token'];
		$key = JWT_SECRET; 
		$decoded = null;
		try {
			$decoded = JWT::decode($jwt, $key, array('HS256'));
		} catch(SignatureInvalidException $e) {
			throw new AuthException("Invalid token");
		}
		return $decoded;
	}

	private static function verifyToken() {
		$decoded = self::decodeToken(); 
		if($decoded->exp < time()) {
			//this won't be ever called as exp is set
			throw new AuthException('token expired, login again');
		}
	}

	private static function createToken($userId) {
		$key = JWT_SECRET; 
		$time = time();
		$token = array(
			"iss" => ENDPOINT,
			"userId" => $userId,
			"aud" => ENDPOINT, 
			"iat" => $time, 
			"exp" => $time + 3600 
		);
		$jwt = JWT::encode($token, $key);
		return $jwt;
	}


}
