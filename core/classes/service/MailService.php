<?php
use SimpleMVC\DBP;
use SimpleMVC\Log;
class MailService {

	public static function changeMailStatus($mailId,$newStatus) {
			
		$query = "select * from mail_to where user = :userId and mailId = :mailId and status != :newStatus";
		$bindings = array();
		$bindings['userId'] = AccessService::getCurrentUser();
		$bindings['mailId'] = $mailId; 
		$bindings['newStatus'] = $newStatus; 
		$mt = DBP::getObject($query,$bindings,'MailTo');
		if($mt) {
			$mt->setStatus($newStatus);
			$mt->save();
		}

		$resp = array('status' => 'success');
		return json_encode($resp);
	}

	public static function saveAsDraft($from, $toString, $subject, $content) {
		$toString = trim($toString);

		$m = new Draft();
		$m->setIsActive(1);
		$m->setSender($from);
		$m->setRcvr($toString);
		$m->setSubject($subject);
		$m->setContent($content);
		$m->save();

		$resp = array('status' => 'success');
		return json_encode($resp);
	}

	private static function createAttachment($path) {
		$a = new Attachment();
		$a->setPath($path);
		return $a;
	}
		

	public static function sendMail($from, $toString, $subject, $content) {
		$ma = null;
		if(isset($_FILES["file"])) {
			$target = "/tmp/".basename($_FILES["file"]["name"]);
			move_uploaded_file($_FILES["file"]["tmp_name"], $target);
			$ma = self::createAttachment($target);
		}
		$toString = trim($toString);
		if(!$toString) {
			$resp = array('error' => 'to should not be empty');
			return json_encode($resp);
		}

		$toArr = explode(",",$toString);
		foreach($toArr as $to) {
			$to = trim($to);
			$user = UserService::getUserFromName($to);
			if(!$user) { 
				$resp = array('error' => "Invalid User $to");
				return json_encode($resp);
			}
		}

		DBP::beginTransaction();

		$m = new Mail();
		$m->setIsActive(1);
		$m->setSender($from);
		$m->setSubject($subject);
		$m->setContent($content);
		$m->save();

		if($ma) {
			$ma->setMailId($m->getId());
			$ma->save();
		}
		if(isset($_POST['attachments'])) {
			$ats = $_POST['attachments'];
			foreach($ats as $aid) {
				$a = self::getAttachment($from, $aid);
				if($a) {
					$a->setId(null);
					$a->setMailId($m->getId());
					$a->save();
				}
			}
		}

		$toArr = explode(",",$toString);
		foreach($toArr as $to) {
			$to = trim($to);
			$mt = new MailTo();
			$mt->setIsActive(1);
			$mt->setMailId($m->getId());
			$user = UserService::getUserFromName($to);
			if(!$user) continue;
			$mt->setUser($user->getId());
			$mt->setStatus('unread');
			$mt->save();
		}	

		DBP::commit();
		$resp = array('status' => 'success');
		return json_encode($resp);
	}

	public static function fetchDrafts($userId) {
		$draftId = null;
		if(isset($_REQUEST['draftId'])) {
			$draftId = $_REQUEST['draftId'];
		}
		$query = "select d.* from drafts d  where d.sender = :userId";
		$bindings = array('userId' => $userId);
		if($draftId) {
			$query .= " and d.id = :draftId";
			$bindings['draftId'] = $draftId;
		}
		$rows = DBP::getResultSet($query,$bindings);
		$mails = array();
		foreach($rows as $row) {
			$mail = array();
			$mail['mailId'] = $row['id'];
			$mail['updatedAt'] = date("d-F-Y H:i:s",$row['updatedAt']);
			$mail['subject'] = $row['subject'];
			$mail['content'] = $row['content'];
			$mail['sender'] = $row['sender']; 
			$mail['rcvr'] = $row['rcvr'];
			$mails[$row['id']] = $mail;
		}
		return json_encode($mails);
	}

	private static function getAttachments($mailId) {
		$query = "select id ,path from attachments where mailId = :mailId";
		$rows = DBP::getResultSet($query,array('mailId' => $mailId));
$arr = array();
		foreach($rows as $row) {
			$arr[] = array('id' => $row['id'], 'name' => basename($row['path']));
		}
		return $arr;
	}

	private static function getAttachment($userId,$aid) {
		$query = "select a.* from attachments a inner join mails m on (m.id = a.mailId) inner join mail_to mt on (mt.mailId = m.id) where mt.user = :userId and a.id = :aid "; 
		$bindings = array('aid' => $aid, 'userId' => $userId);
		$a = DBP::getObject($query, $bindings, 'Attachment');
		return $a;
	}

	public static function downloadAttachment($aid) {
		$userId = AccessService::getCurrentUser();
		$a = self::getAttachment($userId,$aid);
		if($a) {
			$path = $a->getPath();
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($path).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($path));
			readfile($path);
		}
	}

	public static function fetchIncomingMails($userId) {
		$mailId = null;
		if(isset($_REQUEST['mailId'])) {
			$mailId = $_REQUEST['mailId'];
		}
		$query = "select m.createdAt as sentTime,m.subject,m.content,u.name as sender,mt.mailId,mt.status from mails m inner join mail_to mt on (m.id = mt.mailId) inner join users u on (u.id = m.sender) where mt.user = :userId";
		$bindings = array('userId' => $userId);
		if($mailId) {
			$query .= " and m.id = :mailId";
			$bindings['mailId'] = $mailId;
		}
		$rows = DBP::getResultSet($query,$bindings);
		$mails = array();
		foreach($rows as $row) {
			$mail = array();
			$mail['mailId'] = $row['mailId'];
			$mail['sentTime'] = date("d-F-Y H:i:s",$row['sentTime']);
			$mail['subject'] = $row['subject'];
			$mail['content'] = $row['content'];
			$mail['sender'] = $row['sender']; 
			$mail['status'] = $row['status'];
			$mail['attachments'] = self::getAttachments($row['mailId']); 
			$mails[$row['mailId']] = $mail;
		}
		return json_encode($mails);
	}

	public static function deleteDraft($id,$userId) {
		$query = "delete from drafts where id = :id and sender = :userId";
		$bindings = array();
		$bindings['id'] = $id;
		$bindings['userId'] = $userId;
		DBP::runQuery($query,$bindings);

		$resp = array('status' => 'success');
		return json_encode($resp);
	}
	
	public static function fetchSentMails($userId) {
		$query = "select m.subject,m.content,m.id as mailId,u.name as rcvr from mails m inner join mail_to mt on (m.id = mt.mailId) inner join users u on (u.id = mt.user) where m.sender = :userId ";
		$rows = DBP::getResultSet($query,array('userId' => $userId));
		$mails = array();
		foreach($rows as $row) {
			$mailId = $row['mailId'];
			if(isset($mails[$mailId])) {
				$mails[$mailId]['rcvr'] .= ",".$row['rcvr'];
				continue;
			}
			$mail = array();
			$mail['subject'] = $row['subject'];
			$mail['content'] = $row['content'];
			$mail['rcvr'] = $row['rcvr']; 
			$mails[$mailId] = $mail;
		}
		return json_encode($mails);
	}
}
