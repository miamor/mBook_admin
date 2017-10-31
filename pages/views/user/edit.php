<?php
if ($user->id) {
	$page_title = 'Edit user '.$name;

	if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

	include 'pages/views/_temp/'.$page.'/'.$mode.'.php';

} else {
	include 'pages/views/error.php';
}
