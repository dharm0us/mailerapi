<?php
use SimpleMVC\BaseEntity;
class Attachment extends BaseEntity {

	protected $mailId;
	protected $path;

	public function getMailId() {
		return $this->mailId;
	}

	public function setMailId($mailId) {
		$this->mailId = $mailId;
	}

	public function getPath() {
		return $this->path;
	}

	public function setPath($path) {
		$this->path = $path;
	}

	protected static function getFKs() {
		$fks = array();
		$fks['mailId'] = array('table' => 'mails', 'column' => 'id'); 
		return $fks;
	}

	protected static function getColumnDefinitions() {
		$defs = array();
		$defs['mailId'] = 'bigint(20) NOT NULL';
		$defs['path'] = 'varchar(512) not NULL';
		return $defs;
	}

	protected static function getTableName() {
		return "attachments";
	}


}

?>
