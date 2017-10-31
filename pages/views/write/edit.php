<?php
if ($book->id) {
	$page_title = 'Sửa bài viết '.$title;

	include 'objects/genre.php';
	$genre = new Genre();
	$genre->readAll();
	$genList = $genre->all_list;

	if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

	include 'pages/views/_temp/write/mode.'.$mode.'.php';

} else {
	include 'pages/views/error.php';
}