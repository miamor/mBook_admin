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

}

if ($do) include 'pages/system/'.$page.'/'.$do.'.php';
else if ($mode) {
	include 'views/'.$page.'/'.$mode.'.php';
}
else if ($n) {
	if ($user->id) {
		$mode = 'edit';
		include 'views/'.$page.'/'.$mode.'.php';
	} else include 'views/error.php';
} else include 'views/'.$page.'/list.php';
