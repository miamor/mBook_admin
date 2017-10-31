<?php
$ok = true;

$title = isset($_POST['title']) ? $_POST['title'] : null;
if ($title) {
	if ($title != $chapter->title) {
		$chapter->title = $title;
		$theBook = $chapter->sReadOne();
		if ($theBook['id']) {
			echo '[type]error[/type][content]One topic with this title has already existed. Please choose another title if this is different from <a href="'.$theBook['link'].'">'.$title.'</a>[/content]';
			$ok = false;
		}
	}
	if ($ok == true) {
		$chapter->title = $title;
		$chapter->content = $content = isset($_POST['content']) ? $_POST['content'] : null;
		
		if ($content) {
			$update = $chapter->update();
			if ($update) {
				$chapter->link = $chapter->bookLink.'/chapters/'.$chapter->link;
				echo '[type]success[/type][dataID]'.$chapter->link.'[/dataID][content]Chapter updated successfully. Redirecting to <a href="'.$chapter->link.'">'.$title.'</a>...[/content]';
			} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
		} else echo '[type]error[/type][content]Missing parameters![/content]';
	}
} else echo '[type]error[/type][content]Missing parameters[/content]';
