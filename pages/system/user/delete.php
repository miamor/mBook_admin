<?php
if ($user->id) {
	$update = $user->delete();
	if ($update) {
		echo 1;
	} else echo 0;
} else echo -1;
