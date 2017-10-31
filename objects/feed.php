<?php
class Feed extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "posts";

	public function __construct() {
		parent::__construct();
	}

	function getGroupInfo ($g) {
		$query = "SELECT members FROM groups WHERE id = ? LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $g);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function fetchFeed ($type, $u = null, $page = 0) {
		$typ = ($type == 'status') ? '' : $type;
		$order = "modified DESC, created DESC, id DESC";

		$numPerPage = 10;
		$start = $page*$numPerPage;
		$lim = "LIMIT {$start}, {$numPerPage}";

		$query = "SELECT uid,type,id,iid,bid,ilink,gid,created,content FROM
					" . $this->table_name . "
				WHERE
					uid = ? AND type = ?
				ORDER BY
					{$order}
				{$lim}";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $u);
		$stmt->bindParam(2, $typ);
		$stmt->execute();

		$this->all_list = $ar = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$ok = true;
				$row['href'] = '';
				if (!$row['type']) {
					$row['type'] = 'status';
					$row['iid'] = $row['id'];
					if ($row['gid']) {
						$gIn = $this->getGroupInfo($row['gid']);
						$gMem = $gIn['members'];
					//	if (!check('['.$this->u.']', $gMem)) $ok = false;
					}
					if (!isset($row['href']) || !$row['href'])
						$row['href'] = 'status/'.$row['iid'];
				} else if ($row['type'] == 'addbookbox') {
					$row['href'] = 'box/'.$row['bid'].'?b='.$row['iid'];
				} else if ($row['type'] == 'review') {
					$row['href'] = 'review/'.$row['iid'];
				} else if ($row['type'] == 'chapter' || $row['type'] == 'chapter-w') {
					if ($row['type'] == 'chapter') $pLink = $this->bLink;
					else $pLink = $this->wLink;
					if ($row['iid']) $row['href'] = $pLink.'/'.$row['bid'].'/chapters/'.$row['iid'].'?byID=1';
					if ($row['ilink']) $row['href'] = $pLink.'/'.$row['bid'].'/chapters/'.$row['ilink'];
				}

				if ($ok == true) {
					$row['author'] = $this->sGetUserInfo($row['uid']);
					$this->all_list[$u.'_'.$type][] = $row;
				}

			$num = $stmt->rowCount();
		}

		return $this->all_list;
	}

	function sGroup ($gid) {
	}

	function makeHref ($type, $iid, $ilink, $bid) {
		if ($type == 'status') {
			$type = 'status';
			return 'status/'.$iid;
		}
		else if ($type == 'review') {
			return 'review/'.$iid;
		}
		else if ($type == 'chapter' || $type == 'chapter-w') {
			if ($type == 'chapter') $pLink = $this->bLink;
			else $pLink = $this->wLink;
			if ($iid) return $pLink.'/'.$bid.'/chapters/'.$iid.'?byID=1';
			if ($ilink) return $pLink.'/'.$bid.'/chapters/'.$ilink;
		}
		/*else if ($type == 'addbookbox') {
			$href = 'box/'.$row['bid'].'?b='.$row['iid'];
		} */
	}

	function readAll ($type = null, $u = null, $page = 0) {
		if ($type) {
			if ($type == 'status') $con[] = "type = ''";
			else $con[] = "type = '{$type}'";
		}
		else $con[] = " type != 'addbookbox' AND type != 'buy' AND type != 'addbox' ";

		$cons = array();
		if (isset($con)) $cons[] = implode(' OR ', $con);

/*		if ($u) $cons[] = "uid = {$u}";
		else $cons[] = "uid != 0";
*/
//		if (count($cons) > 0) $cond = 'WHERE '.implode(' AND ', $cons);
		if (count($cons) > 0) $cond = implode(' AND ', $cons);

		$order = "modified DESC, created DESC, id DESC";

		$numPerPage = 2;
		$start = $page*$numPerPage;

		$qr = "SELECT t1.*
		FROM " . $this->table_name . " t1
			JOIN (SELECT uid,MAX(id) id FROM " . $this->table_name . " GROUP BY uid,type) t2
			ON t1.id = t2.id AND t1.uid = t2.uid
		WHERE {$cond}
		ORDER BY {$order}
		LIMIT {$start}, {$numPerPage}";
//		echo $qr;

		$st = $this->conn->prepare($qr);
		$st->execute();

		$this->all_list = $ar = array();
		while ($oneU = $st->fetch(PDO::FETCH_ASSOC)) {
			$id = $oneU['id'];
			$uid = $oneU['uid'];
			$type = $oneU['type'];
			if (!$type) $type = 'status';
			$iid = ($type == 'status') ? $oneU['id'] : $oneU['iid'];

			$ok = true;
			if ($type == 'status') {
				if ($oneU['gid']) {
					$gIn = $this->getGroupInfo($oneU['gid']);
					$gMem = $gIn['members'];
					if (!check('['.$this->u.']', $gMem)) $ok = false;
				}
			}
			if ($ok) {
				$oneU['author'] = $this->sGetUserInfo($oneU['uid']);
				$oneU['href'] = $this->makeHref($type, $iid, $oneU['ilink'], $oneU['bid']);
				$this->all_list[$uid.'_'.$type][] = $oneU;
			}

			$query = "SELECT * FROM
						" . $this->table_name . "
					WHERE
						(uid = ? AND type = ? AND iid != ? AND id != ?)
						AND
						({$cond})
					ORDER BY
						{$order}
					LIMIT 0,11";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(1, $uid);
			$stmt->bindParam(2, $oneU['type']);
			$stmt->bindParam(3, $iid);
			$stmt->bindParam(4, $id);
			$stmt->execute();

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$ok = true;
				$riid = ($type == 'status') ? $row['id'] : $row['iid'];
				$row['href'] = $this->makeHref($type, $riid, $row['ilink'], $row['bid']);

				if ($ok == true) {
					$row['author'] = $this->sGetUserInfo($row['uid']);
		//			if ( !array_key_exists($row['uid'].'_'.$row['type'].'_'.$row['gid'], $this->all_list) ) {
		//			if ( $row['type'] != 'status' || !array_key_exists($row['uid'].'_'.$row['type'].'_'.$row['gid'], $this->all_list) ) {
		//				$this->all_list[$row['uid'].'_'.$row['type'].'_'.$row['gid']][] = $row;
		//			}
					$this->all_list[$uid.'_'.$type][] = $row;
				}
			}

			$num = $stmt->rowCount() + 1;
			$this->all_list[$uid.'_'.$type]['num'] = ($num > 10) ? '10+' : $num;
		}
//		print_r($this->all_list);

		return $this->all_list;
	}

	function getHashtagList () {
		$query = "SELECT * FROM hashtag";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$ar = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ar[] = $row;
		}
		return $ar;
	}

	function getReviews ($id) {
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

			$row['content'] = content(substr($row['content'], 0, 1200)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>';
			$row['short_content'] = content(substr(htmlspecialchars(strip_tags($row['content'])), 0, 280)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>';

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
					$uShare[] = $this->sGetUserInfo($oS);
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

	function getReviewsRatings ($id) {
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



	function update() {

		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					price = :price,
					description = :description,
					category_id  = :category_id
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->price=htmlspecialchars(strip_tags($this->price));
		$this->description=htmlspecialchars(strip_tags($this->description));
		$this->category_id=htmlspecialchars(strip_tags($this->category_id));
		$this->id=htmlspecialchars(strip_tags($this->id));

		// bind parameters
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':price', $this->price);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':category_id', $this->category_id);
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
