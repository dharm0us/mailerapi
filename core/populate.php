<?php
require 'common.inc.php';

User::createOrUpdateTable();
Draft::createOrUpdateTable();
Mail::createOrUpdateTable();
MailTo::createOrUpdateTable();
Attachment::createOrUpdateTable();

for($i = 1; $i<=10;$i++) {
	$u = new User();
	$u->setName("user$i");
	$u->setPassword("pass$i");
	$u->setIsActive(1);
	$u->save();
}
