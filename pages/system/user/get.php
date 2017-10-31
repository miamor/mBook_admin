<?php
$pageNum = ($config->get('page')) ? $config->get('page') : 0;

$feed->fetchFeed($m, $id , $pageNum);
$_List = $feed->all_list[$id.'_'.$m];

foreach ($_List as $ok => $oF) {
	$diid = $oF['iid'];
	echo '<div data-type="'.$oF['type'].'" data-iid="'.$diid.'" class="feed-load no-margin"><span class="feed-href hidden">'.$oF['href'].'</span></div>';
}
