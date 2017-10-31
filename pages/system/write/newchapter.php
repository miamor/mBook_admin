<?php
$chapter->title = $title = isset($_POST['title']) ? $_POST['title'] : null;
if ($title) {
	$theBook = $chapter->sReadOne();
	if ($theBook['id']) echo '[type]error[/type][content]One topic with this title has already existed. Please choose another title if this is different from <a href="'.$theBook['link'].'">'.$title.'</a>[/content]';
	else {
		$chapter->title = $title;
		$chapter->bid = $book->id;
		$chapter->content = $content = isset($_POST['content']) ? $_POST['content'] : null;

		if ($title && $content) {
			$create = $chapter->create();
			if ($create) {
				echo '[type]success[/type][dataID]'.$book->link.'/chapters/'.$chapter->link.'[/dataID][content]Topic created successfully. Redirecting to <a href="'.$book->link.'/chapters/'.$chapter->link.'">'.$title.'</a>...[/content]';
			} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
		} else echo '[type]error[/type][content]Missing parameters![/content]';
	}
} else echo '[type]error[/type][content]Missing parameters[/content]';
