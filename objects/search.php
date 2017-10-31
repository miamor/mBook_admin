<?php
class Search extends Config {
	protected $table_name = "books";
	public $isFeed = false;

	public function __construct() {
		parent::__construct();
		require_once(MAIN_PATH.'/include/html_dom.php');
		require_once(MAIN_PATH.'/include/phash.class.php');
		$this->pHash = pHash::Instance();
	}

	function countBooks ($title) {
		$query = "SELECT id FROM " . $this->table_name . " WHERE title = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $title);
		$stmt->execute();
		$num = $stmt->rowCount();

		return $num;
	}

	function searchLocal () {
		$query = "SELECT * FROM " . $this->table_name . " WHERE link = ? OR isbn = ? LIMIT 0,1";
		$link = encodeURL($this->keyword);
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $link);
		$stmt->bindParam(2, $link);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['download'] = explode('|', str_replace('&amp;', '&', $row['download']));

		return $row;
	}

	protected function getReviewsList ($id = '', $order = '') {
		if (!$order) $order = "modified DESC, created DESC, id DESC";
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_reviews
				WHERE
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$totalReview = 0;
		$totalRates = 0;
		$this->ratingsList = array();
		$rateAr = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$row['link'] = $this->rLink.'/'.$row['id'];
			$row['author'] = $this->getUserInfo($row['uid']);

			$cont = htmlspecialchars(strip_tags($row['content']));
			$row['short_content'] = (strlen($cont) > 280) ? content(substr($cont, 0, 280)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row['content_feed'] = (strlen($cont) > 1500) ? content(substr($cont, 0, 1500)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];

			$totalReview++;
			$totalRates += $row['rate'];

/*			$row['ratingsList'] = $this->getReviewsRatings($row['id']);
			$row['ratingsNum'] = count($row['ratingsList']);
			$row['average'] = $this->rAverage;
			$row['total'] = $this->rTotal;
*/
			if (!isset($rateAr[$row['rate']])) $rateAr[$row['rate']] = 0;
			else $rateAr[$row['rate']]++;

			$this->ratingsList[] = $row;
		}

		if ($totalReview == 0) $averageRate = 0;
		else {
			$averageRate = $totalRates/$totalReview;
			if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
			else $averageRate = floor($averageRate);
			$this->totalReview['local'] = $totalReview;
			$this->detailReview['local'] = $rateAr;
		}
		$this->averageRate['local'] = number_format($averageRate, 1);

		if ($totalReview > 0) return $this->ratingsList;
		return false;
	}

	protected function getReviews ($id = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					id,rate
				FROM
					" . $this->table_name . "_reviews
				WHERE
					iid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$totalReview = 0;
		$totalRates = 0;
		$this->ratingsList = array();
		$rateAr = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$totalReview++;
			$totalRates += $row['rate'];

			if (!isset($rateAr[$row['rate']])) $rateAr[$row['rate']] = 0;
			else $rateAr[$row['rate']]++;

			$this->ratingsList[] = $row;
		}

		if ($totalReview == 0) $averageRate = 0;
		else $averageRate = $totalRates/$totalReview;
		if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
		else $averageRate = floor($averageRate);

		$this->averageRate['local'] = number_format($averageRate, 1);
		$this->totalReview['local'] = $totalReview;
		$this->detailReview['local'] = $rateAr;

		return true;
	}

	function search () {
		$this->averageRate['local'] = $this->averageRate['goodreads'] = $this->totalReview['local'] = $this->totalReview['goodreads'] = 0;

		$this->detailReview = array(
			'local' => array(
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
				5 => 0
			),
			'goodreads' => array(
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
				5 => 0
			)
		);

		// searchLocal
		$local = $this->searchLocal();
		if ($local['id']) {
			$this->id = $local['id'];
//			$rvList = $this->getReviews(); // no need anymore, since we use link to load through ajax
			$data['local'] = $local;
		}
		if (isset($local['isbn13']) && $local['isbn13']) {
			$this->ISBN = $local['isbn'];
			// if isbn13 is updated, then use thia func to load ratings only
			$goodreads_ratings = $this->getGoodreadsRating();
		}
		else {
			// or else whole goodreads book data must be fetched
			$goodreads = $this->searchGoodreads();
			$this->ISBN = $goodreads['isbn13'];
		}

		//print_r($local);
		$rvWidget = $goodreads['reviews_widget'];
		unset($goodreads['reviews_widget']);
		$data['goodreads'] = $goodreads;

		$data['title'] = ($local && $local['title']) ? $local['title'] : $goodreads['title'];
		$data['des'] = ($local && $local['des']) ? $local['des'] : $goodreads['des'];
		$data['genres'] = ($local && $local['genres']) ? $local['genres'] : null;
		$data['author_txt'] = ($local && $local['author']) ? '<a href="'.$this->auLink.'/'.encodeURL($local['author']).'">'.$local['author'].'</a>' : $goodreads['authors']['author'][0];
		$data['download'] = ($local && $local['download']) ? $local['download'] : null;

		if ($this->averageRate['local'] > 0 && $this->averageRate['goodreads'] > 0)
			$average = ($this->averageRate['local'] + $this->averageRate['goodreads'])/2;
		else if ($this->averageRate['goodreads'] > 0)
			$average = $this->averageRate['goodreads'];
		else
			$average = $this->averageRate['local'];

		$total = ($this->totalReview['local'] + $this->totalReview['goodreads']);

		for ($i = 1; $i <= 5; $i++) {
			$totalOne = $this->detailReview['local'][$i] + $this->detailReview['goodreads'][$i];
			$detailReview[$i] = array(
				'total' => $totalOne,
				'percent' => ($total > 0) ? number_format(100*$totalOne/$total, 2) : 0
			);
		}

		$data['ratings'] = array(
			'average' => $average,
			'total' => $total,
			'detail' => $detailReview,
			'local' => array(
				'average' => $this->averageRate['local'],
				'total' => $this->totalReview['local'],
				'detail' => $this->detailReview['local'],
				'reviewList' => $this->bLink.'/'.$local['link'].'/reviews?temp=search' // link to load reviews
			),
			'goodreads' => array(
				'average' => $this->averageRate['goodreads'],
				'total' => $this->totalReview['goodreads'],
				'detail' => $this->detailReview['goodreads'],
				'reviewList' => $rvWidget
			),
		);
		$this->response = $data;
		return $data;
	}


	function updateISBN () {
		$query = "SELECT id,title,isbn FROM books WHERE id >= 65";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//			$this->keyword = urlencode($row['title']);
			$this->keyword = mb_strtolower($row['title']);
			$this->bid = $row['id'];
			$goodreads = $this->searchGoodreads();
//			print_r($goodreads);
			$this->ISBN = null;
			if ($goodreads && encodeURL($goodreads['title']) == encodeURL($row['title'])) {
				if ($goodreads['isbn13']) {
					$this->ISBN = $goodreads['isbn13'];
					$this->updateISBN_one($row['id'], $this->ISBN);
				}
			}
			echo $row['title'].' <br/>'.$this->url.' <br/>'.$this->bid.' - '.$this->ISBN.'<hr/>';
		}
	}
	function updateISBN_one ($bid, $ISBN) {
		$query = "UPDATE books SET
				isbn = ?
			WHERE id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $ISBN);
		$stmt->bindParam(2, $bid);
		$stmt->execute();
	}


	function getGoodreadsRating () {
		// review counts (json)
		// https://www.goodreads.com/book/review_counts.json?key='.GOODREADS_KEY.'&isbns=9780749394820
		$data = file_get_contents('https://www.goodreads.com/book/review_counts.json?key='.GOODREADS_KEY.'&isbns='.$this->ISBN);
}

	function searchGoodreads () {
		// get book id by title
//		$url = 'https://www.goodreads.com/book/title.xml?key='.GOODREADS_KEY.'&title='.str_replace(' ', '%20', $this->keyword);
		$this->url = $url = 'https://www.goodreads.com/book/title.xml?key='.GOODREADS_KEY.'&title='.urlencode($this->keyword);
		$xml_string = file_get_contents($url);

		if (!$xml_string) return false;

		$xml = simplexml_load_string($xml_string, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($xml);
		$array = json_decode($json, TRUE);
		$bIn = $array['book'];
		unset($bIn['popular_shelves']);
		unset($bIn['similar_books']);
		unset($bIn['series_works']);
		unset($bIn['book_links']);
		unset($bIn['buy_links']);
		unset($bIn['isbn']);
		unset($bIn['asin']);
		unset($bIn['kindle_asin']);
		unset($bIn['marketplace_id']);

/*		$this->averageRate['goodreads'] = $bIn['average_rating'];
		$this->totalReview['goodreads'] = $bIn['ratings_count'];
		$detailReview = $bIn['work']['rating_dist'];
*/		$detailReview = array();
		$workRate = explode('|', $bIn['work']['rating_dist']);
		$totalRates = 0; $totalReview = 0;
		foreach ($workRate as $k => $wo) {
			if ($wo) {
				$wr = explode(':', $wo);
				$rate = (int)$wr[0];
				$rO = (int)$wr[1];
				if ($rate > 0) {
					$detailReview[$rate] = $rO;
					$totalRates += $rO*$rate;
				} else $totalReview = $rO;
			}
		}
		if ($totalReview == 0) $averageRate = 0;
		else {
			$averageRate = $totalRates/$totalReview;
			if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
			else $averageRate = floor($averageRate);
			$this->totalReview['goodreads'] = $totalReview;
			$this->detailReview['goodreads'] = $detailReview;
		}
		$this->averageRate['goodreads'] = number_format($averageRate, 1);

//		$result = $bIn;
		$workPublicationDay = '';
		if (!is_array($bIn['work']['original_publication_day']))
			$workPublicationDay .= $bIn['work']['original_publication_day'].'/';
		if (!is_array($bIn['work']['original_publication_month']))
			$workPublicationDay .= $bIn['work']['original_publication_month'].'/';
		if (!is_array($bIn['work']['original_publication_year']))
			$workPublicationDay .= $bIn['work']['original_publication_year'];
		$bookPublicationDay = '';
		if (!is_array($bIn['publication_day']))
			$bookPublicationDay .= $bIn['publication_day'].'/';
		if (!is_array($bIn['publication_month']))
			$bookPublicationDay .= $bIn['publication_month'].'/';
		if (!is_array($bIn['publication_year']))
			$bookPublicationDay .= $bIn['publication_year'];
		$result = array(
			'id' => $bIn['id'],
			'title' => $bIn['title'],
			'isbn13' => $bIn['isbn13'],
			'publication_day' => $bookPublicationDay,
			'publisher' => $bIn['publisher'],
			'thumb' => $bIn['image_url'],
			'is_ebook' => $bIn['is_ebook'],
			'language_code' => $bIn['language_code'],
			'des' => $bIn['description'],
			'link' => $bIn['link'],
			'authors' => $bIn['authors'],
			'work' => array(
				'id' => $bIn['work']['id'],
				'title' => $bIn['work']['original_title'],
				'original_publication_day' => $workPublicationDay
			),
			'reviews_widget' => $bIn['reviews_widget'],
		);

		$bookID = $result['id'];

/*		// get description and other info of book and work
		$bookXml = file_get_contents('https://www.goodreads.com/book/show/'.$bookID.'.xml?key='.GOODREADS_KEY);
//		echo $bookXml.' - fuck';
		$bIn = json_decode(json_encode(simplexml_load_string($bookXml, 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);
		$bIn = $bIn['id'];
		print_r($bIn);
*/
		return $result;
	}

	function searchGoogle () {

		// Query the book database by ISBN code.
//		isbn = isbn || "9781451648546"; // Steve Jobs book
		$this->ISBN = 8936024916299;
		if ($this->ISBN) {
			$url = "https://www.googleapis.com/books/v1/volumes?q=isbn:".$this->ISBN;
			$json = file_get_contents($url);
			$result = json_decode($json, TRUE);

			if ($result['totalItems']) {
				// There'll be only 1 book per ISBN
				$book = $result['items'][0];

				$title = ($book["volumeInfo"]["title"]);
				$subtitle = ($book["volumeInfo"]["subtitle"]);
				$authors = ($book["volumeInfo"]["authors"]);
				$printType = ($book["volumeInfo"]["printType"]);
				$pageCount = ($book["volumeInfo"]["pageCount"]);
				$publisher = ($book["volumeInfo"]["publisher"]);
				$publishedDate = ($book["volumeInfo"]["publishedDate"]);
				$webReaderLink = ($book["accessInfo"]["webReaderLink"]);

				// For debugging
				print_r($book);

			}
		}
	}


	function resize ($file, $w = 800, $h = 800, $crop = false) {
		list($width, $height) = getimagesize($file);
		$r = $width / $height;
		if ($crop) {
			if ($width > $height) {
				$width = ceil($width-($width*abs($r-$w/$h)));
			} else {
				$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		} else {
			if ($w/$h > $r) {
				$newwidth = $h*$r;
				$newheight = $h;
			} else {
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
		$src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		// save to output
		$outputFile = $file;
		imagejpeg($dst, $outputFile);
		return $outputFile;
	}

	function upload ($file, $isFront = true) {
		$ar = explode('.', $file['name']);
		$ext = end($ar);

		$name = explode('.'.$ext, $file['name'])[0];
		if (!$isFront) $name .= '_back';
		$randomCode = generateRandomString();
		$fname = "search_{$name}_{$randomCode}.{$ext}";
		$new_path = MAIN_PATH.'/data/img/books/'.$fname;
		if ($file['error'] > 0) {
			echo 'File upload error: '.$file['error'];
			return false;
		} else {
			move_uploaded_file($file['tmp_name'], $new_path);
		}
		return $new_path;
	}

	function getCoverCode () {
		$img = $this->resize($this->book_coverIMG);
		$pixels = $this->pHash->getPixImage($img);
		$pixStr = implode(',', $pixels);

		// generate code
		$this->book_coverCode = $pixStr.',[?]';

		$dataFile = MAIN_PATH.'/data/books.txt';
		// search this book cover code in data file
		if (!exec('grep '.escapeshellarg($this->book_coverCode).' '.$dataFile)) {
			// if not existed;
			file_put_contents($dataFile, $this->book_coverCode.PHP_EOL , FILE_APPEND | LOCK_EX);
		}

		return $this->book_coverCode;
	}

	function getBooksToGetCover () {
		$query = "SELECT title,cover_code FROM
					boxes_books
				GROUP BY link
				ORDER BY id DESC";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$this->booksList = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->booksList[] = $row;
		}
		return $this->booksList;
	}

	function searchByCover () {
		if (!$this->book_coverCode) $this->getCoverCode();
		$thisCoverCode = substr($this->book_coverCode, 0, -4);
		$books = $this->getBooksToGetCover();
		$thisCodes = explode(',', $thisCoverCode);
		$minDistance = 100000;
		foreach ($books as $ob) {
			$obCoverCode = $ob['cover_code'];
//			$obCoverCode = str_replace(','.end(explode(',', $ob['cover_code'])), '', $ob['cover_code']);
			$obCodes = explode(',', $obCoverCode);
			$distance = 0;
			foreach ($thisCodes as $key => $oneCode) {
				$obOneCode = $obCodes[$key];
				$t = ($obOneCode - $oneCode);
				$distance += $t*$t;
			}
			$distance = sqrt($distance);
			if ($distance < $minDistance) {
				$minDistance = $distance ;
				$bookTit = $ob['title'];
			}
//			echo $obCoverCode.' ~ '.$distance.'<br/>';
		}
//		echo '<br/> |||||| '.$thisCoverCode.' - '.$minDistance.' - '.$bookTit.'<br/>';
	}

}
