<?php
$page_title = "Đánh giá sách";

if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

if ($config->get('book')) {
	$bookID = $config->get('book');
//	$bIn = $review->sGetBookInfo($bookID);
	$bIn = $review->getBookInfo($bookID);
} else {
	$review->getBookList();
}

$config->addJS('dist', 'ratings.min.js');
$config->addJS('dist', $page.'/new.js');

if ($do) include 'pages/system/'.$page.'/'.$do.'.php';
else include 'pages/views/_temp/'.$page.'/new.php';
