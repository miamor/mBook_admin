<?php
$ok = true;

$username = isset($_POST['username']) ? $_POST['username'] : null;
if ($username) {
	if ($username != $user->username) {
		$user->username = $username;
		$theBook = $user->sReadOneByUsername();
		if ($theBook['id']) {
			echo '[type]error[/type][content]One user with this @username has already existed. Please choose another username</a>[/content]';
			$ok = false;
		}
	}
	if ($ok == true) {
		$user->username = $username;
		$user->first_name = $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : null;
		$user->last_name = $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : null;
		$user->coins = $coins = isset($_POST['coins']) ? $_POST['coins'] : 0;
		$user->avatar = $avatar = isset($_POST['avatar']) ? $_POST['avatar'] : null;
		$user->oauth_uid = $oauth_uid = isset($_POST['oauth_uid']) ? $_POST['oauth_uid'] : null;
		$user->email = $email = isset($_POST['email']) ? $_POST['email'] : null;
		$user->type = $type = isset($_POST['type']) ? $_POST['type'] : 0;

		if ($username && $first_name && $last_name) {
			$name = $last_name.' '.$first_name;
			$create = $user->update();
			if ($create) {
				echo '[type]success[/type][dataID]'.$user->link.'[/dataID][content]User updated successfully. Redirecting to <a href="'.$user->link.'">'.$name.'</a>...[/content]';
			} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
		} else echo '[type]error[/type][content]Missing parameters![/content]';
	}
} else echo '[type]error[/type][content]Missing parameters[/content]';
