<?php
class Book extends BookWrite {

	public function __construct() {
		parent::__construct();
	}

	// create product
	function create() {
		//write query
			$query = "INSERT INTO
					" . $this->table_name . "
				SET
					title = ?, link = ?, des = ?, genres = ?, author = ?, published = ?, download = ?, `show` = ?, type = ?, added_uid = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->des = content($this->des);
		$this->link = encodeURL($this->title);

		//echo $this->title.'~'.$this->link.'~'.$this->des.'~'.$this->genres.'~'.$this->author.'~'.$this->published.'~'.$this->download_link.'~'.$this->status;

		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->link);
		$stmt->bindParam(3, $this->des);
		$stmt->bindParam(4, $this->genres);
		$stmt->bindParam(5, $this->author);
		$stmt->bindParam(6, $this->published);
		$stmt->bindParam(7, $this->download_link);
		$stmt->bindParam(8, $this->status);
		$stmt->bindParam(9, $this->type);
		$stmt->bindParam(10, $this->u);

		if ($stmt->execute()) {
			$this->link = $this->bLink.'/'.$this->link;
			return true;
		} else
			return false;
	}


	function getBorrow ($uidAr = null, $bidAr = null, $status = -1, $from_record_num = 0, $records_per_page = 24) {
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
		$bidAr = array_values($bidAr);
		if (count($bidAr) > 0) {
			$conBAr = array();
			foreach ($bidAr as $bO) {
				$conBAr[] = "`bid` = '{$bO}' ";
			}
			$con[] = '('.implode(' OR ', $conBAr).')';
		}

		if ($status > 0) $con[] = 'stt = '.$status;

		if (count($con) > 0) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					*
				FROM
					storage_borrow
				{$cond}
				ORDER BY
					created DESC, id DESC
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['user'] = $this->getUserInfo($row['uid']);
			$row['book'] = $this->sReadOneByID($row['bid']);
			$all_list[] = $row;
		}
		return $all_list;
	}

	function countBorrow ($uidAr = null, $bidAr = null, $status = -1) {
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
		$bidAr = array_values($bidAr);
		if (count($bidAr) > 0) {
			$conBAr = array();
			foreach ($bidAr as $bO) {
				$conBAr[] = "`bid` = '{$bO}' ";
			}
			$con[] = '('.implode(' OR ', $conBAr).')';
		}

		if ($status > 0) $con[] = 'stt = '.$status;

		if (count($con) > 0) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					id
				FROM
					storage_borrow
				{$cond}
				ORDER BY
					created DESC, id DESC
				";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt->rowCount();
	}


	function getDonations ($uidAr = null, $bidAr = null, $from_record_num = 0, $records_per_page = 24) {
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
		$bidAr = array_values($bidAr);
		if (count($bidAr) > 0) {
			$conBAr = array();
			foreach ($bidAr as $bO) {
				$conBAr[] = "`bid` = '{$bO}' ";
			}
			$con[] = '('.implode(' OR ', $conBAr).')';
		}

		if (count($con) > 0) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					*
				FROM
					donations
				{$cond}
				ORDER BY
					created DESC, id DESC
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['user'] = $this->getUserInfo($row['uid']);
			$row['book'] = $this->sReadOneByID($row['bid']);
			$all_list[] = $row;
		}
		return $all_list;
	}

	function countDonations ($uidAr = null, $bidAr = null) {
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
		$bidAr = array_values($bidAr);
		if (count($bidAr) > 0) {
			$conBAr = array();
			foreach ($bidAr as $bO) {
				$conBAr[] = "`bid` = '{$bO}' ";
			}
			$con[] = '('.implode(' OR ', $conBAr).')';
		}

		if (count($con) > 0) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					id
				FROM
					donations
				{$cond}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt->rowCount();
	}

	function getDonationsBook ($from_record_num = 0, $records_per_page = 24) {
		$lim = '';
		if ($records_per_page > 0)
			$lim = "LIMIT {$from_record_num}, {$records_per_page}";

		$query = "SELECT
					*
				FROM
					donations
				WHERE
					bid = ?
				ORDER BY
					created DESC, id DESC
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();

		$all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['user'] = $this->getUserInfo($row['uid']);
			$row['book'] = $this->sReadOneByID();
			$all_list[] = $row;
		}
		return $all_list;
	}

	function countDonationsBook () {
		$query = "SELECT
					id
				FROM
					donations
				WHERE
					bid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();

		return $stmt->rowCount();
	}

	function countDonationsBookNum () {
		$query = "SELECT
					id,num
				FROM
					donations
				WHERE
					bid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();

		$num = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$num += $row['num'];
		}
		return $num;
	}

	function sReadAll () {
		$query = "SELECT
					title,id
				FROM
					" . $this->table_name . "
				";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$list[] = $row;
		}
		return $list;
	}

	function readAll ($withU = -1, $genresAr = null, $authorAr = null, $order = '', $from_record_num = 0, $records_per_page = 24, $keyword = null, $status = -1, $in_storage = -1, $searchInStorage = false) {
		$lim = '';
		$con = array();

		if ($records_per_page > 0)
			$lim = "LIMIT {$from_record_num}, {$records_per_page}";

		//$con[] = 'authenticated = 1';
		$con[] = 'type = 0';
		if ($withU == 1) $con[] = 'uid != 0';
		if ($withU == 0) $con[] = 'uid = 0';

		if ($keyword) {
			$con[] = "INSTR(title, '{$keyword}') > 0";
		}

		if ($in_storage > -1) $con[] = "in_storage = 1";

		if ($genresAr) {
			$conGenAr = array();
			foreach ($genresAr as $gO) {
				$conGenAr[] = "INSTR(genres, '[{$gO}]') > 0";
			}
			$con[] = '('.implode(' OR ', $conGenAr).')';
		}
		if ($authorAr) {
			$conAuAr = array();
			foreach ($authorAr as $aO) {
				$conAuAr[] = "author = '{$aO}' ";
			}
			$con[] = '('.implode(' OR ', $conAuAr).')';
		}
		if ($status != -1) $con[] = '`show` = '.$status;
		if ($con) $cond = 'WHERE '.implode(' AND ', $con);

		if (!$order) $order = "modified DESC, created DESC, id DESC, title ASC";

		$query = "SELECT
					title,link,id,type,published,status,thumb,genres,created,modified,uid,author,in_storage,added_uid,`show`
				FROM
					" . $this->table_name . "
				{$cond}
				ORDER BY
					{$order}
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$this->all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->bLink.'/'.$row['link'];

			$row['uid'] = (int)$row['uid'];
			// author
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);
			else $row['author'] = array('name' => $row['author'], 'link' => $this->auLink.'/'.encodeURL($row['author']));

			$row['added'] = $this->getUserInfo($row['added_uid']);

			if ($row['in_storage']) {
				/*$donated_users = explode('|', $row['donated_uid']);
				$row['num_in_storage'] = 0;
				foreach ($donated_users as $dno) {
					$dno = explode('-', $dno);
					$row['num_in_storage'] += $dno[1];
				}*/
				$row['num_in_storage'] = $this->countDonationsBookNum();
			}

			if ($searchInStorage) {
				$firstCharacter = encodeURL(strtolower(substr($row['title'], 0, 1)));

				$this->all_list[$firstCharacter][] = $row;
			} else {
				//$row['des'] = content(substr(htmlspecialchars(strip_tags($row['des'])), 0, 240)).'... <a href="'.$row['link'].'" class="small">Xem đầy đủ</a>';
				/*
				// share list
				$row['shareNum'] = 0;
				//$row['share'] = array();
				if ($row['share']) {
					$shareAr = explode(',', $row['share']);
					foreach ($shareAr as $oS)
						$uShare[] = $this->getUserInfo($oS);
					$row['share'] = $uShare;
					$row['shareNum'] = count($shareAr);
				}
				*/
				// genres
				if ($row['genres']) {
					$gnr = explode(',', $row['genres']);
					$gAr = $gTxtAr = array();
					foreach ($gnr as $gno) {
						$gIn = $this->getGenre($gno);
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

				// reviews
				$this->getReviews($row['id']);
				$row['averageRate'] = $this->averageRate;
				$row['totalReview'] = $this->totalReview;

				// status
				$row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Đang tiến hành</span>' : '<span class="text-danger">Đã hoàn thành</span>';
				$row['sttTextIcon'] = ($row['status'] == 0) ? '<span class="text-success fa fa-refresh"></span>' : '<span class="text-danger fa fa-check hidden"></span>';

				// chapters num
				//$row['chaptersNum'] = $this->countChapters($row['id']);

				$this->all_list[] = $row;
			}
		}

		return $stmt;
	}

	function countAll ($withU = -1, $genresAr = null, $authorAr = null, $keyword, $status = -1, $in_storage = -1) {
		$con = array();

		//$con[] = 'authenticated = 1';
		$con[] = 'type = 0';
		if ($withU == 1) $con[] = 'uid != 0';
		if ($withU == 0) $con[] = 'uid = 0';

		if ($keyword) {
			$con[] = "INSTR(title, '{$keyword}') > 0";
		}

		if ($in_storage > -1) $con[] = "in_storage = 1";

		if ($genresAr) {
			$conGenAr = array();
			foreach ($genresAr as $gO) {
				$conGenAr[] = "INSTR(genres, '[{$gO}]') > 0";
			}
			$con[] = '('.implode(' OR ', $conGenAr).')';
		}
		if ($authorAr) {
			$conAuAr = array();
			foreach ($authorAr as $aO) {
				$conAuAr[] = "author = '{$aO}' ";
			}
			$con[] = '('.implode(' OR ', $conAuAr).')';
		}
		if ($status != -1) $con[] = '`show` = '.$status;
		if ($con) $cond = 'WHERE '.implode(' AND ', $con);

		$query = "SELECT
					id
				FROM
					" . $this->table_name . "
				{$cond}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt->rowCount();
	}

	function readOne () {
		preg_match('!\d+!', $this->id, $matches);
		if ($this->id == $matches[0]) {
			$query = "SELECT * FROM " . $this->table_name . "
				WHERE
					id = ?
				LIMIT 0,1";
			$val = $this->id;
		} else if ($this->id) {
			$query = "SELECT * FROM " . $this->table_name . "
				WHERE
					link = ?
				LIMIT 0,1";
			$val = $this->id;
		} else if ($this->title) {
			$query = "SELECT * FROM " . $this->table_name . "
				WHERE
					title = ?
				LIMIT 0,1";
			$val = $this->title;
		}
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $val);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];

		if ($row['id']) {
			$this->link = $row['link'] = $this->bLink.'/'.$row['link'];
			$this->title = $row['title'];
			$this->type = $row['type'];

			// des
			$row['des'] = content($row['des']);
			$row['download'] = explode('|', str_replace('&amp;', '&', $row['download']));

			// is published
			if ($row['published'] == 1) {
				$row['publishedList'] = $this->bPublished();
			} else { // not published, get requests
				$rq = explode(',', $row['requests']);
				foreach ($rq as $ro)
					$rqAr[] = $this->getUserInfo($ro);
				$row['requests'] = $rqAr;
			}

			if ($row['in_storage']) {
				/*$donated_users = explode('|', $row['donated_uid']);
				$row['num_in_storage'] = 0;
				foreach ($donated_users as $dk => $dno) {
					$dno = explode('-', $dno);
					$row['num_in_storage'] += $dno[1];
					$row['donated_user'][$dk] = $this->getUserInfo($dno[0]);
					$row['donated_user'][$dk]['num'] = $dno[1];
				}*/
				$row['num_in_storage'] = $this->countDonationsBookNum();
				$donatedList = $this->getDonationsBook();
				foreach ($donatedList as $dno) {
					$row['donated_user'][] = $dno['user'];
				}
				$row['borrowList'] = $this->getBorrowList();
			}

			// share list
			$row['shareNum'] = 0;
			$row['share'] = array();
			// author
			$row['uid'] = (int)$row['uid'];
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);
			else $row['author'] = array('name' => $row['author'], 'link' => $this->auLink.'/'.encodeURL($row['author']));

			// genres
			if ($row['genres']) {
				$gnr = explode(',', $row['genres']);
				$gAr = $gTxtAr = array();
				foreach ($gnr as $gno) {
					$gIn = $this->getGenre($gno);
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

			// status
			if ($row['type'] == 0) $row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Đang tiến hành</span>' : '<span class="text-danger">Đã hoàn thành</span>';
			else $row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Mở</span>' : '<span class="text-danger">Khóa</span>';

			// quotes
			$row['quotesNum'] = 0;
			$row['quotesAr'] = array();
			if ($row['quotes']) {
				$row['quotesAr'] = explode('[!#!]', content($row['quotes']));
				$row['quotesNum'] = count($row['quotesAr']);
			}

			// ratings
			$this->getReviews();
			$row['ratingsList'] = $this->ratingsList;
			$row['ratingsNum'] = count($this->ratingsList);
		}

		return $row;
	}

	function getDonationOne ($id) {
		$query = "SELECT
					*
				FROM
					donations
				WHERE
					id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['user'] = $this->getUserInfo($row['uid']);
		$row['book'] = $this->sReadOneByID($row['bid']);
		return $row;
	}

	function updateDonation($id, $uid, $bid, $num) {
		$query = "UPDATE
					donations
				SET
					uid = :uid,
					bid = :bid,
					num = :num
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':uid', $uid);
		$stmt->bindParam(':bid', $bid);
		$stmt->bindParam(':num', $num);
		$stmt->bindParam(':id', $id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	function getBorrowOne ($id) {
		$query = "SELECT
					*
				FROM
					storage_borrow
				WHERE
					id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['user'] = $this->getUserInfo($row['uid']);
		$row['book'] = $this->sReadOneByID($row['bid']);
		return $row;
	}

	function updateBorrow ($id, $uid, $bid, $stt) {
		$query = "UPDATE
					storage_borrow
				SET
					uid = :uid,
					bid = :bid,
					stt = :stt
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':uid', $uid);
		$stmt->bindParam(':bid', $bid);
		$stmt->bindParam(':stt', $stt);
		$stmt->bindParam(':id', $id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	function addDonation($uid, $bid, $num) {
		$query = "INSERT INTO
				donations
			SET
				uid = ?, bid = ?, num = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $uid);
		$stmt->bindParam(2, $bid);
		$stmt->bindParam(3, $num);
		$bIn = $this->sReadOneByID($bid);

		if ($stmt->execute()) {
			//$this->changeBookDonation($uid, $bid, $num);
			$coinsForWhomDonated = COINS_DONATE_ONE_BOOK*$num;
			$this->addCoin($coinsForWhomDonated, $uid);
			// add noti for user who writes this
			$valAr = array(
					'from_uid' => 0,
					'type' => 'add-coin-donation',
					'post_id' => $this->id, // book id
					'content' => json_encode(array(
								'content' => 'donate book',
								'book_title' => $bIn['title'],
								'donated_num' => $num,
								'coins_added' => $coinsForWhomDonated
							), JSON_UNESCAPED_UNICODE)
				);
			$this->addNoti($valAr, $uid);

			return true;
		} else {
			return false;
		}
	}

	function getDonated ($bid) {
		$query = "SELECT
					donated_uid
				FROM
					books
				WHERE
					id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $bid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['donated_uid'];
	}

	function changeBookDonation ($uid, $bid, $num) {
		$donated = $this->getDonated($bid);
		$query = "UPDATE
					books
				SET
					donated_uid = :donated_uid
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		if ($donated) $donated .= '|'.$uid.'-'.$num;
		else $donated = $uid.'-'.$num;
		$stmt->bindParam(':donated_uid', $donated);
		$stmt->bindParam(':id', $id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	function addBorrow($uid, $bid, $stt) {
		$query = "INSERT INTO
				storage_borrow
			SET
				uid = ?, bid = ?, stt = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->des = content($this->des);

		// bind values
		$stmt->bindParam(1, $uid);
		$stmt->bindParam(2, $bid);
		$stmt->bindParam(3, $stt);

		if ($stmt->execute()) {
			if ($stt == 2) {
				$coinsForWhomReturned = COINS_BORROW_RETURN;
				$this->addCoin($coinsForWhomReturned, $uid);
				// add noti for user who writes this
				$valAr = array(
					'from_uid' => 0,
					'type' => 'add-coin-returned',
					'post_id' => $this->id, // book id
					'content' => json_encode(array(
								'content' => 'returned',
								'book_title' => $bIn['title'],
								'coins_added' => $coinsForWhomReturned
							), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $uid);
			}
			return true;
		} else {
			return false;
		}
	}

	function deleteBorrow ($id) {
		$query = "DELETE FROM storage_borrow WHERE id = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		if ($result = $stmt->execute()) return true;
		else return false;
	}

	function deleteDonation ($id) {
		$query = "DELETE FROM donations WHERE id = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		if ($result = $stmt->execute()) return true;
		else return false;
	}

	function getBorrowList () {
		$query = "SELECT
					*
				FROM
					storage_borrow
				WHERE
					bid = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		$list = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['user'] = $this->getUserInfo($row['uid']);
			if ($row['stt'] == 0) $row['sttTxt'] = 'Đang đợi đến lượt';
			if ($row['stt'] == 1) $row['sttTxt'] = 'Đang mượn';
			if ($row['stt'] == 2) $row['sttTxt'] = 'Đã mượn và đã hoàn trả';
			$row['reviewID'] = $this->getReviewID($row['uid']);
			if ($row['reviewID']) $row['reviewLink'] = $this->rLink.'/'.$row['reviewID'];
			$list[] = $row;
		}
		return $list;
	}

	function getReviewID ($uid) {
		$query = "SELECT
					id
				FROM
					books_reviews
				WHERE
					iid = ? AND uid = ?
				ORDER BY
					created DESC
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $uid);
		$stmt->execute();
		$list = array();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['id'];
	}

	function checkRegister () {
		$query = "SELECT id FROM
				storage_borrow
			WHERE bid = ? AND uid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->u);

		$stmt->execute();
		return $stmt->rowCount();
	}

	function register () {
		$query = "INSERT INTO
				storage_borrow
			SET
				bid = ?, uid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->u);

		if ($stmt->execute()) {
			return true;
		} else {
			return false;
		}
	}

	function unregister () {
		$query = "DELETE FROM storage_borrow
			WHERE bid = ? AND uid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->u);

		if ($stmt->execute()) {
			return true;
		} else {
			return false;
		}
	}

	function bPublished ($id = '', $order = '') {
		if (!$order) $order = "published_day DESC, id DESC";
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_published
				WHERE
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$bPublished = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['author'] = $this->getUserInfo($row['pid']);
			$row['link'] = $row['author']['link'].'/published/'.$row['id'];
			$row['des'] = content(substr(htmlspecialchars(strip_tags($row['des'])), 0, 140)).'...';
			$bPublished[] = $row;
		}

		return $bPublished;
	}

}
?>
