<?php
// include object files
include_once 'objects/user.php';

// prepare product object
$user = new User();

// get ID of the product to be edited
//$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
$id = isset($n) ? $n : '';
$m = (isset($__pageAr[2])) ? $__pageAr[2] : null;
if (isset($id) && $id) {
	// set ID property of product
	$user->id = $id;

	// read the details of product
	$uView = $user->readOne();
	extract($uView);

	// Reset $id to ID property of product
	$id = $user->id;

	if (!$m) $m = 'home';
	if ($m == 'reviews') {
		include_once 'objects/feed.php';
		$feed = new Feed();
		$stmt = $feed->fetchFeed('review', $id);
		$user->feedReviewsList = (isset($feed->all_list[$id.'_review'])) ? $feed->all_list[$id.'_review'] : array();
	} 
	else if ($m == 'status') {
		include_once 'objects/feed.php';
		$feed = new Feed();
		$feed->fetchFeed('status', $id);
		$user->feedStatusList = (isset($feed->all_list[$id.'_status'])) ? $feed->all_list[$id.'_status'] : array();
	} 
}

if ($do) include 'system/'.$page.'/'.$do.'.php';
else {
	if (!isset($id) || !$id) include 'views/'.$page.'/list.php';
	else if ($m != 'settings' || $config->u === $user->id) {
		include 'views/'.$page.'/view.php';
	}
	else include 'error.php';
}
