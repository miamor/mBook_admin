<?php
$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
$bid = isset($_POST['bid']) ? $_POST['bid'] : null;
$num = isset($_POST['num']) ? $_POST['num'] : 0;

if ($uid && $bid && $num) {
	$update = $book->addDonation($uid, $bid, $num);
	if ($update) {
		echo '[type]success[/type][dataID]'.$config->bLink.'?type=storage&mode=donations[/dataID][content]Donation added successfully. Redirecting...[/content]';
	} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
} else echo '[type]error[/type][content]Missing parameters![/content]';
