<?php
if ($book->id) {
	$update = $book->delete();
	if ($update) {
		echo 1;
	} else echo 0;
} else echo -1;
