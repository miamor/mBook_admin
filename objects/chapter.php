<?php
class Chapter extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "books_chapters";

	public $coins = 0;
	public $bChapters = array();
	public $isFeed = false;

	public function __construct() {
		parent::__construct();
	}

	// create product
	function create () {
		//write query
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					title = ?, link = ?, content = ?, uid = ?, iid = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->link = encodeURL($this->title);

		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->link);
		$stmt->bindParam(3, $this->content);
		$stmt->bindParam(4, $this->u);
		$stmt->bindParam(5, $this->bid);

		if ($stmt->execute()) {
			if ($this->addPost()) {
				return true;
			} else return false;
		} else
			return false;
	}

	protected function addPost () {
		$type = "chapter";
		if ($this->pageType == 'write') $type .= "-w";
		$query = "INSERT INTO
					posts
				SET
					type = ?, uid = ?, ilink = ?, bid = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $type);
		$stmt->bindParam(2, $this->u);
		$stmt->bindParam(3, $this->link);
		$stmt->bindParam(4, $this->bid);

		if ($stmt->execute()) {
			// add coin for adding new chapter
			$this->addCoin(COINS_NEW_CHAPTER);
			// add noti for user who writes this
			$valAr = array(
					'type' => 'new-chapter',
					'iid' => $this->link,
					'content' => json_encode(array(
								'book_id' => $this->bid,
								'chapter_id' => $this->link,
								'coins_added' => COINS_NEW_CHAPTER
							), JSON_UNESCAPED_UNICODE)
				);
			$this->addNoti($valAr);

			return true;
		} else return false;
	}

	function sReadOne () {
		$query = "SELECT id, link FROM
					" . $this->table_name . "
				WHERE
					title = ? AND iid = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->bid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function sReadCmtOne () {
		$query = "SELECT id FROM
					" . $this->table_name . "_reviews
				WHERE
					content = ? AND iid = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->rContent);
		$stmt->bindParam(2, $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function reply () {
		$query = "INSERT INTO
					" . $this->table_name . "_reviews
				SET
					content = ?, rate = ?, bid = ?, cid = ?, uid = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->rContent);
		$stmt->bindParam(2, $this->rate);
		$stmt->bindParam(3, $this->bid);
		$stmt->bindParam(4, $this->id);
		$stmt->bindParam(5, $this->u);

//		echo $this->table_name.'_reviews ~ '.$this->rContent.' ~ '.$this->rate.' ~ '.$this->bid.' ~ '.$this->id.' ~ '.$this->u;

		if ($stmt->execute()) {
				// add coin for user who writes this
				$coinsForWhomRated = (COINS_RATE_USER_WRITE_CHAPTER*$this->rate)/5;
				$this->addCoin($coinsForWhomRated, $this->uid);
				// add coin for user who rates ($this->u)
				$this->addCoin(COINS_RATE_CHAPTER);
				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'rate-chapter',
						'iid' => $this->id,
						'post_id' => $this->bid,
						'content' => json_encode(array(
									'rate' => $this->rate,
									'content' => htmlspecialchars(strip_tags($this->rContent)),
									'book_id' => $this->bid,
									'chapter_id' => $this->id,
									'chapter_link' => $this->link,
									'book_title' => $this->bookTitle,
									'chapter_title' => $this->title,
									'coins_added' => $coinsForWhomRated
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);
			return true;
		} else return false;
	}

	public function countAll () {
		$query = "SELECT id FROM " . $this->table_name . "";

		$stmt = $this->conn->prepare( $query );
		$stmt->execute();

		$num = $stmt->rowCount();

		return $num;
	}

	function readAll ($order = '') {
		if (!$order) $order = "modified DESC, created DESC, id DESC";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->bid);

		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['link'] = $this->bookLink.'/chapters/'.$row['link'];
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);

			$row['content'] = content($row['content']);

			$row['coins'] = $this->getCoins($row['id']);
			$this->bChapters[] = $row;
		}

		return $stmt;
	}

	function readOne ($byID = false) {
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					(n = ? OR link = ?) AND iid = ?
				LIMIT 0,1";

		preg_match('!\d+!', $this->id, $matches);
		if ($this->id == $matches[0] || $byID) $query = "SELECT * FROM
					" . $this->table_name . "
				WHERE
					id = ? AND iid = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);

		if ($this->id == $matches[0] || $byID) {
			$stmt->bindParam(1, $this->id);
			$stmt->bindParam(2, $this->bid);
		} else {
			$stmt->bindParam(1, $this->id);
			$stmt->bindParam(2, $this->id);
			$stmt->bindParam(3, $this->bid);
		}

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		if ($row['id']) {
			$this->title = $row['title'];
			$this->uid = $row['uid'];
			$row['link'] = $this->link = $this->bookLink.'/chapters/'.$row['link'];

			// author
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);

			// content
			$row['content'] = content($row['content']);
			$row['content_feed'] = (strlen($row['content']) > 1500) ? content(substr(htmlspecialchars(strip_tags($row['content'], '<br>')), 0, 1500)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];

			$row['shareNum'] = $this->getShareNum();

			// ratings
			$row['ratingsList'] = $this->getRatings();
			$row['ratingsNum'] = $this->rTotal;
	//		$row['ratingsList'] = $this->ratingsList;
	//		$row['ratingsNum'] = $this->countRatings();

			$row['coins'] = $this->coins;
		}
		return $row;
	}


	function getShareNum () {
		$query = "SELECT id FROM ".$this->table_name."_share WHERE iid = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		return $stmt->rowCount();
	}
	function checkMyShareFB () {
		$query = "SELECT id FROM ".$this->table_name."_share WHERE iid = ? AND uid = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->u);
		$stmt->execute();
		return $stmt->rowCount();
	}
	function shareFB () {
		if ($this->checkMyShareFB() < 1) {
			$query = "INSERT INTO ".$this->table_name."_share SET uid = ?, iid = ?, fb_post_id = ?";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(1, $this->u);
			$stmt->bindParam(2, $this->id);
			$stmt->bindParam(3, $this->fb_post_id);
			if ($stmt->execute()) {
				// add coin for user who writes this
				$this->addCoin(COINS_SHARE_USER_WRITE_CHAPTER, $this->uid);
				// add coin for user who shares ($this->u)
				$this->addCoin(COINS_SHARE_CHAPTER);
				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'share-chapter',
						'iid' => $this->u,
						'post_id' => $this->bid,
						'content' => json_encode(array(
									'book_id' => $this->bid,
									'chapter_id' => $this->id,
									'chapter_link' => $this->link,
									'fb_post_id' => $this->fb_post_id,
									'coins_added' => COINS_SHARE_USER_WRITE_CHAPTER
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);

				return true;
			}
			else return false;
		} else return false;
	}


	function getRatings ($id = '', $order = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_reviews
				WHERE
					bid = ? AND cid = ?";

		$valAr = array($this->bid, $id);
//		$this->ratingsList = $this->_getRatings($query, $valAr, true, $this->isFeed);
		$this->ratingsList = $this->_getRatings($query, $valAr);

		return $this->ratingsList;
	}

/*	function countRatings ($id = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_reviews
				WHERE
					bid = ? AND cid = ?";

		$valAr = array($this->bid, $id);
		$num = $this->_countRatings($query, $valAr);

		return $num;
	}
*/
	function getCoins ($id = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					rate,uid
				FROM
					" . $this->table_name . "_reviews
				WHERE
					bid = ? AND cid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->bid);
		$stmt->bindParam(2, $id);

		$stmt->execute();

		$cCoins = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			// set coins
			$row['coins'] = 5;

			// add coins to the chapter
			$cCoins += $row['coins'];
		}

		return $cCoins;
	}


	function doEdit () {

	}


	function update() {
		parent::getTimestamp();

		$query = "UPDATE
					" . $this->table_name . "
				SET
					title = :title,
					content = :content,
					link  = :link,
					modified  = :modified
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->content = $this->content;
		$this->link = encodeURL($this->title);

//		echo $this->title.'~'.$this->content.'~'.$this->link.'~'.$this->timestamp.'~'.$this->id;

		// bind parameters
		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':content', $this->content);
		$stmt->bindParam(':link', $this->link);
		$stmt->bindParam(':modified', $this->timestamp);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	// delete the product
	function delete() {

		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		if ($result = $stmt->execute()) return true;
		else return false;
	}

}
?>
