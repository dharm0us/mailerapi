<?php
use SimpleMVC\BaseEntity;
class Mail extends BaseEntity {

	protected $sender;
	protected $subject;
	protected $content;

	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function getSender() {
		return $this->sender;
	}

	public function setSender($sender) {
		$this->sender = $sender;
	}

	protected static function getFKs() {
		$fks = array();
		$fks['sender'] = array('table' => 'users', 'column' => 'id'); 
		return $fks;
	}

	protected static function getColumnDefinitions() {
		$defs = array();
		$defs['sender'] = 'bigint(20) NOT NULL';
		$defs['subject'] = 'varchar(1024) DEFAULT NULL';
		$defs['content'] = 'text DEFAULT NULL';
		return $defs;
	}

	protected static function getTableName() {
		return "mails";
	}

}

?>
