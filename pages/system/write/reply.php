<?php
// this is reply chapter
$chapter->rContent = $content = isset($_POST['content']) ? $_POST['content'] : null;

if ($content) {
	$theRv = $chapter->sReadCmtOne();
	if ($theRv['id']) echo '[type]error[/type][content]Spam detected![/content]';
	else {
		$chapter->rContent = $content = isset($_POST['content']) ? $_POST['content'] : null;
		$chapter->rate = $rate = isset($_POST['rate']) ? $_POST['rate'] : 0;
		$chapter->bookTitle = $book->title;

		if ($rate) {
			$reply = $chapter->reply();
			if ($reply) {
				$coinsForWhomRated = (COINS_RATE_USER_WRITE_REVIEW*$chapter->rate)/5;
				// inside coin is coins added for whom rated
				echo '[type]success[/type][coin]'.$coinsForWhomRated.'[/coin][dataID]'.$content.'[/dataID][content]Success![/content]';
			} else echo '[type]error[/type][content]Oops! Something went wrong with our system. Please contact the administrators for furthur help.[/content]';
		} else echo '[type]error[/type][content]Missing parameters![/content]';
	}
} else echo '[type]error[/type][content]Missing parameters[/content]';
