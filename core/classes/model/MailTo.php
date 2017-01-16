<?php
use SimpleMVC\BaseEntity;
class MailTo extends BaseEntity {

	protected $mailId;
	protected $user;
	protected $status;

	public function getMailId() {
		return $this->mailId;
	}

	public function setMailId($mailId) {
		$this->mailId = $mailId;
	}

	public function getUser() {
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	protected static function getFKs() {
		$fks = array();
		$fks['mailId'] = array('table' => 'mails', 'column' => 'id'); 
		$fks['user'] = array('table' => 'users', 'column' => 'id'); 
		return $fks;
	}

	protected static function getColumnDefinitions() {
		$defs = array();
		$defs['mailId'] = 'bigint(20) NOT NULL';
		$defs['user'] = 'bigint(20) NOT NULL';
		$defs['status'] = "ENUM ('unread','read','trashed') not null";
		return $defs;
	}

	protected static function getTableName() {
		return "mail_to";
	}
}

?>
