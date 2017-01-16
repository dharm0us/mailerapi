<?php
use SimpleMVC\BaseController;
class MailController extends BaseController {

	protected function setMaps() {
		$this->GET_Map['fetchIncomingMails'] = function() {
			echo MailService::fetchIncomingMails(AccessService::getCurrentUser());
		};
		$this->GET_Map['downloadAttachment'] = function() {
			$aid = $_REQUEST['aid'];
			MailService::downloadAttachment($aid);
		};
		$this->POST_Map['deleteDraft'] = function() {
			$draftId = $_POST['draftId'];
			echo MailService::deleteDraft($draftId,AccessService::getCurrentUser());
		};
		$this->GET_Map['fetchDrafts'] = function() {
			echo MailService::fetchDrafts(AccessService::getCurrentUser());
		};
		$this->GET_Map['fetchSentMails'] = function() {
			echo MailService::fetchSentMails(AccessService::getCurrentUser());
		};
		$this->POST_Map['sendMail'] = function() {
			$to = $_POST['to'];
			$subject = $_POST['subject'];
			$content = $_POST['content'];
			echo MailService::sendMail(AccessService::getCurrentUser(),$to,$subject,$content);
		};
		$this->POST_Map['saveAsDraft'] = function() {
			$to = $_POST['to'];
			$subject = $_POST['subject'];
			$content = $_POST['content'];
			echo MailService::saveAsDraft(AccessService::getCurrentUser(),$to,$subject,$content);
		};
		$this->POST_Map['changeMailStatus'] = function() {
			$newStatus = $_POST['newStatus'];
			$mailId = $_POST['mailId'];
			echo MailService::changeMailStatus($mailId,$newStatus);
		};
	}

	public function run() {
		AccessService::ensureLogin();
		parent::run();
	}
}

?>
