<?php
$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
$bid = isset($_POST['bid']) ? $_POST['bid'] : null;
$stt = isset($_POST['stt']) ? $_POST['stt'] : 0;

if ($uid && $bid) {
	$update = $book->updateBorrow($_id, $uid, $bid, $stt);
	if ($update) {
		echo '[type]success[/type][dataID]'.$config->bLink.'?type=borrow&mode=edit&id='.$_id.'[/dataID][content]Borrow request updated successfully. Redirecting...[/content]';
	} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
} else echo '[type]error[/type][content]Missing parameters![/content]';
