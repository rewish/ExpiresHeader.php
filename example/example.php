<?php
require_once dirname(__FILE__) . '/../ExpiresHeader.php';

try {
	$EH = new ExpiresHeader();
	// Must
	$EH->setFile($_GET['file']);
	// Optional
	$EH->setConfig(array(
		'days' => 7,
		'gzip' => false
	));
	// Optional
	$EH->setMimeType(array(
		'html' => 'text/html',
		'css'  => 'text/css'
	));
	$EH->display();
} catch (ExpiresHeaderException $e) {
	header('HTTP', true, $e->getCode());
	echo $e->getMessage();
	exit(0);
}
