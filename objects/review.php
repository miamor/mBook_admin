<?php
class Review extends Config {
	private $table_name = "books_reviews";
	public $isFeed = false;

	public function __construct() {
		parent::__construct();
	}

	function upload ($file, $isFront = true) {
		$ar = explode('.', $file['name']);
		$ext = end($ar);

		$name = explode('.'.$ext, $file['name'])[0];
		if (!$isFront) $name .= '_back';
		$randomCode = generateRandomString();
		$fname = "{$name}_{$randomCode}.{$ext}";
		$new_path = MAIN_PATH.'/data/img/books/'.$fname;
		if ($file['error'] > 0) {
			echo 'File upload error: '.$file['error'];
			return false;
		} else {
			move_uploaded_file($file['tmp_name'], $new_path);
		}
		return $new_path;
	}

	function create () {
		$thumbURL = (isset($this->thumbPath) && $this->thumbPath) ? str_replace(MAIN_PATH, MAIN_URL, $this->thumbPath) : '';

		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					content = ?, rate = ?, iid = ?, uid = ?, to_fb = ?, thumb = ?, title = ?, `show` = ?";

		$stmt = $this->conn->prepare($query);

		$this->content = content($this->content);

		// bind values
		$stmt->bindParam(1, $this->content);
		$stmt->bindParam(2, $this->rate);
		$stmt->bindParam(3, $this->iid);
		$stmt->bindParam(4, $this->u);
		$stmt->bindParam(5, $this->toFB);
		$stmt->bindParam(6, $thumbURL);
		$stmt->bindParam(7, $this->title);
		$stmt->bindParam(8, $this->status);

		//echo $this->content.'~'.$this->iid.'~'.$this->rate.'~'.$this->u;
		if ($stmt->execute()) {
			// read id of new post
			$newPost = $this->sReadOne();
			$this->id = $newPost['id'];
			$this->link = $newPost['link'];

			$type = "review";
			$q = "INSERT INTO posts SET type = ?, iid = ?, uid = ?, gid = ?";
			$st = $this->conn->prepare($q);
			if (!isset($this->gid)) $this->gid = 0;
			// bind values
			$st->bindParam(1, $type);
			$st->bindParam(2, $this->id);
			$st->bindParam(3, $this->u);
			$st->bindParam(4, $this->gid);
			if ($st->execute()) {
				// add coin for adding new review
				$this->addCoin(COINS_NEW_REVIEW);
				// add noti for user who writes this
				$coinsAdded = ($this->toFB) ? COINS_NEW_REVIEW : COINS_NEW_REVIEW + COINS_NEW_REVIEW_TO_FACEBOOK;
				if ($this->toFB) {
					$this->addCoin(COINS_NEW_REVIEW_TO_FACEBOOK);
				}
				$valAr = array(
						'type' => 'new-review',
						'iid' => $this->id,
						'content' => json_encode(array(
									'book_title' => $this->bookTitle,
									'review_id' => $this->id,
									'coins_added' => $coinsAdded
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr);
				return true;
			}
		}
		return false;
	}

	function updateFB_postID () {
		$query = "UPDATE
					" . $this->table_name . "
				SET
					fb_post_id = :fb_post_id
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':fb_post_id', $this->fb_post_id);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}



	function getAll ($uidAr = null, $bidAr = null, $avai = 1, $from_record_num = 0, $records_per_page = 24) {
		$lim = '';
		if ($records_per_page > 0)
			$lim = "LIMIT {$from_record_num}, {$records_per_page}";

		$cond = '';
		$con = array();

		$uidAr = array_values($uidAr);
		if (count($uidAr) > 0) {
			$conUAr = array();
			foreach ($uidAr as $aO) {
				$conUAr[] = "`uid` = '{$aO}' ";
			}
			$con[] = '('.implode(' OR ', $conUAr).')';
		}

		if ($avai == 0) {
			$con[] = "iid = '' OR iid = null";
		} else if ($avai == 1) {
			$bidAr = array_values($bidAr);
			if (count($bidAr) > 0) {
				$conBAr = array();
				foreach ($bidAr as $bO) {
					$conBAr[] = "iid = '{$bO}' ";
				}
				$con[] = '('.implode(' OR ', $conBAr).')';
			}
		}

		if (count($con) > 0) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					*
				FROM
					".$this->table_name."
				{$cond}
				ORDER BY
					created DESC, id DESC
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$this->all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			//$row['author'] = $this->getUserInfo($row['uid']);
			$row = $this->make($row);
			$row['book'] = $this->getBookInfo($row['iid']);
			$this->all_list[] = $row;
		}
		return $this->all_list;
	}

	function countAll ($uidAr = null, $bidAr = null, $avai = 1) {
		$cond = '';
		$con = array();

		$uidAr = array_values($uidAr);
		if (count($uidAr) > 0) {
			$conUAr = array();
			foreach ($uidAr as $aO) {
				$conUAr[] = "`uid` = '{$aO}' ";
			}
			$con[] = '('.implode(' OR ', $conUAr).')';
		}

		if ($avai == 0) {
			$con[] = "iid = '' OR iid = null";
		} else if ($avai == 1) {
			$bidAr = array_values($bidAr);
			if (count($bidAr) > 0) {
				$conBAr = array();
				foreach ($bidAr as $bO) {
					$conBAr[] = "iid = '{$bO}' ";
				}
				$con[] = '('.implode(' OR ', $conBAr).')';
			}
		}

		if (count($con) > 0) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					id
				FROM
					".$this->table_name."
				{$cond}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt->rowCount();
	}



	function sReadOne () {
		$query = "SELECT id FROM
					" . $this->table_name . "
				WHERE
					content = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$this->content = content($this->content);
		$stmt->bindParam(1, $this->content);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['link'] = $this->link = $this->rLink.'/'.$row['id'];
		return $row;
	}

	function sReadCmtOne () {
		$query = "SELECT id FROM
					" . $this->table_name . "_ratings
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
					" . $this->table_name . "_ratings
				SET
					content = ?, rate = ?, iid = ?, uid = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->rContent);
		$stmt->bindParam(2, $this->rate);
		$stmt->bindParam(3, $this->id);
		$stmt->bindParam(4, $this->u);

		//echo $this->table_name.'_ratings ~ '.$this->rContent.' ~ '.$this->rate.' ~ '.$this->id.' ~ '.$this->u;

		if ($stmt->execute()) {
				// add coin for user who writes this
				$coinsForWhomRated = (COINS_RATE_USER_WRITE_CHAPTER*$this->rate)/5;
				$this->addCoin($coinsForWhomRated, $this->uid);
				// add coin for user who rates ($this->u)
				$this->addCoin(COINS_RATE_REVIEW);
				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'rate-review',
						'post_id' => $this->id,
						'content' => json_encode(array(
									'rate' => $this->rate,
									'content' => htmlspecialchars(strip_tags($this->rContent)),
									'book_title' => $this->bookTitle,
									'review_id' => $this->id,
									'coins_added' => $coinsForWhomRated
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);

			return true;
		}
		else return false;
	}

	function make ($row) {
		$row['link'] = $this->rLink.'/'.$row['id'];
		$row['author'] = $this->getUserInfo($row['uid']);

		// content
		$row['content'] = content($row['content']);

		$this->getReviewsRatings($row['id']);
		$row['ratingsList'] = $this->ratingsList;
		$row['ratingsNum'] = $row['total'] = $this->rTotal;
		$row['average'] = $this->rAverage;

		// coins for this review
		$row['coins'] = $this->rCoins;

		return $row;
	}

	function readAll ($iid = null, $u = null, $order = null) {
		if (!$order) $order = "modified DESC, created DESC, id DESC";
		if (!$iid && $this->iid) $iid = $this->iid;
		if ($iid) $con[] = "iid = {$iid}";
		if ($u) $con[] = "uid = {$u}";
		$cond = implode(' AND ', $con);

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					{$cond}
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);

		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row = $this->make($row);
		//	if (!$row['href'] && !$row['type']) $row['href'] = 'review/'.$row['id'];
			$this->ratingsList[] = $row;
		}

		return $stmt;
	}

	function readOne () {
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					id = ?
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->id = $row['id'];
		if ($row['id']) {
			/*$book = new Book();
			$book->id = $row['iid'];*/
			if ($row['iid']) $row['book'] = $this->getBookInfo($row['iid']);
			$row['link'] = $this->link = $this->rLink.'/'.$row['id'];
			$this->uid = $row['uid'];
			$this->bookTitle = $row['title'];
			$toFB_link = 'https://www.facebook.com/'.$this->me['oauth_uid'].'/posts/'.$row['fb_post_id'];
			$this->toFB_html = ($row['fb_post_id']) ? '<a href="'.$toFB_link.'"><i class="fa fa-facebook-square"></i> '.$row['fb_post_id'].'</a>' : null;

			$text = $row['content'];
			//$row['content'] = content($text);

			//$breaks = array("<br />","<br>","<br/>");
			//$text = str_ireplace($breaks, "\r\n", $text);
			//$row['content_feed'] = (strlen($text) > 1500) ? content(substr(htmlspecialchars(strip_tags($text)), 0, 1500)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row['content_feed'] = (strlen($text) > 1500) ? content(substr(strip_tags($text,'<br>'), 0, 1500)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row = $this->make($row);

			// share list
			$row['shareNum'] = $this->getShareNum();
			/*$row['share'] = array();
			if ($row['share']) {
				$shareAr = explode(',', $row['share']);
				foreach ($shareAr as $oS)
					$uShare[] = $this->getUserInfo($oS);
				$row['share'] = $uShare;
				$row['shareNum'] = count($shareAr);
			} */
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
				$this->addCoin(COINS_SHARE_USER_WRITE_REVIEW, $this->uid);
				$this->addCoin(COINS_SHARE_REVIEW);

				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'share-review',
						'iid' => $this->u,
						'post_id' => $this->id,
						'content' => json_encode(array(
									'review_id' => $this->id,
									'fb_post_id' => $this->fb_post_id,
									'coins_added' => COINS_SHARE_USER_WRITE_REVIEW
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);
				return true;
			}
			else return false;
		} else return false;
	}


	function getReviewsRatings ($id = '', $order = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_ratings
				WHERE
					iid = ?";

		$valAr = array($id);
		$this->ratingsList = $this->_getRatings($query, $valAr);

		return $this->ratingsList;
	}

/*	function countReviewsRatings ($id = '') {
		if (!$id) $id = $this->id;
		$query = "SELECT * FROM " . $this->table_name . "_ratings WHERE iid = ?";
		$valAr = array($id);
		$num = $this->_countRatings($tbl, $valAr);
		return $num;
	}
*/

	function getBookInfo ($id = '') {
		$query = "SELECT
					id,thumb,title,link,uid,published,status,author,genres,in_storage
				FROM
					books
				WHERE
					id = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['title']) {
			$row['link'] = $this->bLink.'/'.$row['link'];

			// author
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);
			else $row['author'] = array('name' => $row['author'], 'link' => $this->auLink.'/'.encodeURL($row['author']));

			if ($row['in_storage']) {
				$donated_users = explode('|', $row['donated_uid']);
				$row['num_in_storage'] = 0;
				foreach ($donated_users as $dno) {
					$dno = explode('-', $dno);
					$row['num_in_storage'] += $dno[1];
				}
			}

			// genres
			if ($row['genres']) {
				$gnr = explode(',', $row['genres']);
				$gAr = $gTxtAr = array();
				foreach ($gnr as $gno) {
					$gIn = $this->getBookGenre($gno);
					if (isset($gIn['id'])) {
						$gAr[] = $gIn;
						$gTxtAr[] = '<a href="'.$gIn['link'].'">'.$gIn['title'].'</a>';
					}
				}
				if (count($gAr) > 0) {
					$row['genres'] = $gAr;
					$row['genresText'] = implode(', ', $gTxtAr);
				}
			}
			if (!isset($row['genresText'])) {
				$row['genres'] = array();
				$row['genresText'] = '';
			}
			// ratings
			$this->getBookReviews($id);
			$row['averageRate'] = $this->bAverage;
			$row['totalReview'] = $this->bTotal;

			// status
			$row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Đang tiến hành</span>' : '<span class="text-danger">Đã hoàn thành</span>';
		}

		return $row;
	}

	function getBookList () {
		$query = "SELECT
					id,title,link,uid,authenticated
				FROM
					books
				WHERE
					type = 0 AND status = 1
				ORDER BY
					title ASC";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$this->bookList = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->bookList[$row['authenticated']][] = $row;
		}

		return $this->bookList;
	}

	function sGetBookInfo ($id = '') {
		$query = "SELECT
					title,link,uid
				FROM
					books
				WHERE
					id = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['title']) {
			$row['link'] = $this->bLink.'/'.$row['link'];
		}

		return $row;
	}

	function getBookGenre ($g = 0) {
		$g = intval(preg_replace('/[^0-9]+/', '', $g), 10);
		$query = "SELECT
					title,link,id
				FROM
					genres
				WHERE
					id = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $g);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['link'] = $this->gnLink.'/'.$row['link'];

		return $row;
	}

	function getBookReviews ($iid = '', $order = '') {
		if (!$order) $order = "modified DESC, created DESC, id DESC";

		$query = "SELECT
					rate
				FROM
					books_reviews
				WHERE
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $iid);

		$stmt->execute();

		$totalReview = 0;
		$totalRates = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$totalReview++;
			$totalRates += $row['rate'];
		}

		if ($totalReview == 0) $averageRate = 0;
		else $averageRate = $totalRates/$totalReview;
		if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
		else $averageRate = floor($averageRate);

		$this->bAverage = number_format($averageRate, 1);
		$this->bTotal = $totalReview;

		return $stmt;
	}

	function update () {
		parent::getTimestamp();

		$query = "UPDATE
					" . $this->table_name . "
				SET
					content = :content,
					rate = :rate,
					title = :title,
					iid = :iid,
					`show` = :show,
					modified  = :modified
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':content', $this->content);
		$stmt->bindParam(':rate', $this->rate);
		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':iid', $this->iid);
		$stmt->bindParam(':modified', $this->timestamp);
		$stmt->bindParam(':show', $this->status);
		$stmt->bindParam(':id', $this->id);

		$this->link = $this->rLink.'/'.$this->id;
		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}


}
