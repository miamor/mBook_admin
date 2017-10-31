<?php
if ($_id) {
	$update = $book->deleteDonation($_id);
	if ($update) {
		echo 1;
	} else echo 0;
} else echo -1;
