<?php
if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

include 'objects/user.php';
$user = new User();
$user->readAll();
$uList = $user->all_list;

$bList = $book->sReadAll();

if ($_id) {
	$borrow = $book->getBorrowOne($_id);
	extract($borrow);
	//$config->addJS('dist', 'book/borrow.edit.js');
	include 'pages/views/_temp/'.$page.'/'.$type.'.'.$mode.'.php';
}
