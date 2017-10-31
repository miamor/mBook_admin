<?php
class BookWrite extends Config {
	public $table_name = "books";

	// object properties
	public $id;
	public $title;
	public $link;
	public $content;
	public $cid;
	public $uid;
	public $views;
	public $author;
	public $sid;

	public function __construct() {
		parent::__construct();
	}

	function sReadOne () {
		$query = "SELECT id,type,link FROM
					" . $this->table_name . "
				WHERE
					title = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->title);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['type'] == 1) $this->link = $this->wLink.'/'.$row['link'];
		else $this->link = $this->bLink.'/'.$row['link'];
		return $row;
	}

	function sReadOneByID ($id = null) {
		if (!$id) $id = $this->id;
		$query = "SELECT id,type,link,title,thumb FROM
					" . $this->table_name . "
				WHERE
					id = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['type'] == 1) $row['link'] = $this->wLink.'/'.$row['link'];
		else $row['link'] = $this->bLink.'/'.$row['link'];
		return $row;
	}

	protected function getGenre ($g = 0) {
		$g = intval(preg_replace('/[^0-9]+/', '', $g), 10);
		$query = "SELECT
					*
				FROM
					genres
				WHERE
					id = ? OR link = ? OR title = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $g);
		$stmt->bindParam(2, $g);
		$stmt->bindParam(3, $g);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['link'] = $this->gnLink.'/'.$row['link'];
		return $row;
	}

	protected function countChapters ($iid) {
		if (!$iid) $iid = $this->id;
		$query = "SELECT id FROM " . $this->table_name . "_chapters WHERE iid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $iid);
		$stmt->execute();
		$num = $stmt->rowCount();

		return $num;
	}

	function getStoreInfo ($url) {
		$query = "SELECT * FROM stores WHERE url = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $url);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['address'] = explode('|', $row['address']);
		return $row;
	}

	function getSellStores () {
		$query = "SELECT * FROM books_buy_online WHERE bid = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		$ar = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['sIn'] = $this->getStoreInfo($row['slink']);
			$ar[] = $row;
		}
		return $ar;
	}

	protected function getReviews ($id = '', $order = '') {
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
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->rLink.'/'.$row['id'];
			$row['author'] = $this->getUserInfo($row['uid']);

			//$row['content'] = content(substr($row['content'], 0, 1200)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>';
			$cont = htmlspecialchars(strip_tags($row['content']));
			$row['short_content'] = (strlen($cont) > 280) ? content(substr($cont, 0, 280)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row['content_feed'] = (strlen($cont) > 1500) ? content(substr($cont, 0, 1500)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];

			$totalReview++;
			$totalRates += $row['rate'];

			$row['ratingsList'] = $this->getReviewsRatings($row['id']);
			$row['ratingsNum'] = count($row['ratingsList']);
			$row['average'] = $this->rAverage;
			$row['total'] = $this->rTotal;

			// share list
			$row['shareNum'] = 0;
			$row['share'] = array();
			if ($row['share']) {
				$shareAr = explode(',', $row['share']);
				foreach ($shareAr as $oS)
					$uShare[] = $this->getUserInfo($oS);
				$row['share'] = $uShare;
				$row['shareNum'] = count($shareAr);
			}

			// coins for this review
			$row['coins'] = $this->rCoins;

			$this->ratingsList[] = $row;
		}

		if ($totalReview == 0) $averageRate = 0;
		else $averageRate = $totalRates/$totalReview;
		if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
		else $averageRate = floor($averageRate);

		$this->averageRate = number_format($averageRate, 1);
		$this->totalReview = $totalReview;

		return $stmt;
	}

	protected function getReviewsRatings ($id = '', $order = '') {
		if (!$order) $order = "modified DESC, created DESC, id DESC";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_reviews_ratings
				WHERE
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$totalReview = 0;
		$totalRates = 0;
		$ratingsList = array();
		$this->rCoins = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			//$row['link'] = $this->rLink.'/'.$row['id'];
			$row['author'] = $this->getUserInfo($row['uid']);

			$row['content'] = content($row['content'], 0, 310);

			$totalReview++;
			$totalRates += $row['rate'];

			// set coins for review got rated
			$row['coins'] = 5;
			$this->rCoins += $row['coins'];

			$ratingsList[] = $row;
		}

		if ($totalReview == 0) $averageRate = 0;
		else $averageRate = $totalRates/$totalReview;
		if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
		else $averageRate = floor($averageRate);

		$this->rAverage = number_format($averageRate, 1);
		$this->rTotal = $totalReview;

		return $ratingsList;
	}

	public function update() {
		$query = "UPDATE
					" . $this->table_name . "
				SET
					title = :title,
					genres = :genres,
					thumb = :thumb,
					author = :author,
					des = :des,
					`show` = :show,
					download = :download
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title=htmlspecialchars(strip_tags($this->title));
		$this->genres=htmlspecialchars(strip_tags($this->genres));
		$this->thumb=htmlspecialchars(strip_tags($this->thumb));
		$this->author=htmlspecialchars(strip_tags($this->author));
		$this->download=htmlspecialchars(strip_tags($this->download));
		$this->des=content($this->des);
		$this->id=htmlspecialchars(strip_tags($this->id));

		// bind parameters
		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':genres', $this->genres);
		$stmt->bindParam(':thumb', $this->thumb);
		$stmt->bindParam(':author', $this->author);
		$stmt->bindParam(':download', $this->download);
		$stmt->bindParam(':des', $this->des);
		$stmt->bindParam(':show', $this->status);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	public function delete() {
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		if ($result = $stmt->execute()) return true;
		else return false;
	}

}
