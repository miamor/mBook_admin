<?php

if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

	$config->addJS('plugins', 'DataTables/datatables.min.js');

if ($vPage) {
	$config->addJS('dist', 'ratings.min.js');
	$config->addJS('dist', $page.'/view.'.$vPage.'.js');
	if ($m) {
		// get [bLink]/test-the-display/chapters/chuong-2-ten-chuong-2?temp=feed
		if ($temp == 'feed') include 'pages/views/_temp/book/view.'.$vPage.'.one.feed.php';
		else include 'pages/views/_temp/book/view.'.$vPage.'.one.php';
	} else include 'pages/views/_temp/book/view.'.$vPage.'.php';
} else {
	if ($bView['type'] == 1) { // is topic
		include 'pages/views/_temp/book/view.topic.php';
	} else {
		include 'pages/views/_temp/book/view.php';
	}
}
/*		if ($type == 0) { // write book
		} else {
			if ($vPage) {
				if ($m) include 'pages/views/_temp/'.$page.'/view.'.$vPage.'.one.php';
				else include 'pages/views/_temp/'.$page.'/view.'.$vPage.'.php';
			} else include 'pages/views/_temp/'.$page.'/view.php';
		}
*/