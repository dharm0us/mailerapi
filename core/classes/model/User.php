<?php
use SimpleMVC\BaseEntity;
class User extends BaseEntity {

	protected $name;
	protected $password;

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	protected static function getColumnDefinitions() {
		$defs = array();
		$defs['name'] = 'varchar(32) not NULL';
		$defs['password'] = 'varchar(32) not NULL';
		return $defs;
	}

	protected static function getTableName() {
		return "users";
	}
}

?>
