<?php
$book->title = $title = isset($_POST['title']) ? $_POST['title'] : null;
if ($title) {
	$theBook = $book->sReadOne();
	if ($theBook['id']) echo '[type]error[/type][content]One topic with this title has already existed. Please choose another title if this is different from <a href="'.$theBook['link'].'">'.$title.'</a>[/content]';
	else {
		$book->title = $title;
		$book->des = $des = isset($_POST['des']) ? $_POST['des'] : null;
		$book->cover = $cover = isset($_POST['cover']) ? $_POST['cover'] : null;
		$book->type = $type = isset($_POST['type']) ? $_POST['type'] : 0;

		$book->status = $status = isset($_POST['status']) ? $_POST['status'] : 0;
		$book->published = $published = isset($_POST['published']) ? $_POST['published'] : 0;
		$book->download_link = $download_link = isset($_POST['link']) ? preg_replace('/\s+/', '|', str_replace(array("\r\n","\r","\n"),' ',trim($_POST['link']))) : null;

		if (isset($_POST['author'])) {
			$author = $_POST['author'];
		} else {
			$author = (isset($_POST['author_text'])) ? ($_POST['author_text']) : null;
		}
		$book->author = $author;

		$genres = array();
		if (isset($_POST['genres'])) {
			foreach ($_POST['genres'] as $oL) {
				$genres[] = '['.$oL.']';
			}
		}
		$book->genres = implode(',', $genres);

		if ($title && $des && ($type == 1 || $book->genres) && $book->author) {
			$create = $book->create($type);
			if ($create) {
				echo '[type]success[/type][dataID]'.$book->link.'[/dataID][content]Topic created successfully. Redirecting to <a href="'.$book->link.'">'.$title.'</a>...[/content]';
			} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
		} else echo '[type]error[/type][content]Missing parameters![/content]';
	}
} else echo '[type]error[/type][content]Missing parameters[/content]';
