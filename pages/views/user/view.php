<?php
// set page headers
$page_title = $user->name;
if ($m == 'reviews') $page_title .= "'s reviews";
if ($m == 'status') $page_title .= "'s status";
if ($m == 'settings') $page_title .= " settings";

if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

echo '<div class="u-view">';
include 'pages/views/_temp/'.$page.'/view.sidebar.php';

echo '	<div class="col-lg-9 u-main no-padding-right">';
	include 'pages/views/_temp/'.$page.'/view.'.$m.'.php';
echo '</div>';

echo '<div class="clearfix"></div>
	</div>';
	
if ($m == 'reviews') {
	$config->addJS('dist', 'ratings.min.js');
	$config->addJS('dist', 'user/fetchFeed.js');
}
if ($m == 'status') {
	$config->addJS('dist', 'ratings.min.js');
	$config->addJS('dist', 'user/fetchFeed.js');
}
if ($m == 'settings') {
	$config->addJS('dist', 'user/settings.js');
}
