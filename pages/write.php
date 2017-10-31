<?php
//$id = isset($n) ? $n : ''; // just use $n

include_once 'objects/book.write.php';
include_once 'objects/write.php';
include_once 'objects/chapter.php';

// prepare product object
$book = new Write();
$chapter = new Chapter();

$vPage = (isset($__pageAr[2])) ? $__pageAr[2] : null;
$m = (isset($__pageAr[3])) ? $__pageAr[3] : null;

if ($n) {

	// get book data
	$book->id = $n;
	$bView = $book->readOne();
	extract($bView);

	if ($book->id) {
		if ($bView['type'] == 1) $chapter->pageType = 'write';
		if ($authenticated == 1 && !$do) {
			echo '<script>window.location.href = window.location.href.replace("/write/", "/book/");</script>';
		} else {
			$page_title = $title;

			if (!$vPage || ($vPage == 'chapters' && !$m)) {
				// get chapters
				$chapter->bid = $book->id;
				$chapter->bookLink = $book->link;
				$chapter->readAll();
				$bChapters = $chapter->bChapters;
			}
			if ($vPage == 'chapters') {
				if ($m) {
					// get chapter data
					$chapter->bid = $book->id;
					$chapter->bookLink = $book->link;
					$chapter->id = $m;
					if ($temp == 'feed') $chapter->isFeed = true; 
					$bChap = $chapter->readOne();
			
					$page_title = $bChap['title'];
				} else $page_title = $title.' danh sách chương';
			}
		}
	}
} else {
	$page_title = "Chuyên mục viết";
}

if ($do) include 'pages/system/'.$page.'/'.$do.'.php';
else if ($mode) include 'views/write/'.$mode.'.php';
else if ($n) {
	if ($book->id) {
		include 'views/'.$page.'/view.php';
	} else {
		include 'views/error.php';
	}
} else {
	include 'views/'.$page.'/list.php';
}
