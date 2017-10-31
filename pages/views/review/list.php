<?php
$page_title = "Đánh giá";

if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

include 'objects/user.php';
$user = new User();
$user->readAll();
$uList = $user->all_list;

include 'objects/book.write.php';
include 'objects/book.php';
$book = new Book();
$bList = $book->sReadAll();

	//$config->addJS('plugins', 'DataTables/datatables.min.js');
	$config->addJS('dist', 'review/list.js');
	include 'pages/views/_temp/'.$page.'/list.php';
