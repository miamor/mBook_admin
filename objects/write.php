<?php
class Write extends BookWrite {

	public function __construct() {
		parent::__construct();
	}

	// create product
	function create ($type = 0) {
		//write query
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					title = ?, link = ?, des = ?, genres = ?, uid = ?, type = ?, thumb = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->des = $this->des;
		$this->link = encodeURL($this->title);
		
		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->link);
		$stmt->bindParam(3, $this->des);
		$stmt->bindParam(4, $this->genres);
		$stmt->bindParam(5, $this->u);
		$stmt->bindParam(6, $type);
		$stmt->bindParam(7, $this->cover);

		if ($stmt->execute()) {
			return true;
		} else return false;
	}

	function readAll ($auth = -1, $genresAr = null, $authorAr = null, $order = '', $from_record_num = 0, $records_per_page = 24, $keyword) {
		$lim = '';
		$con = array();
		if ($from_record_num) $lim = "LIMIT
					{$from_record_num}, {$records_per_page}";

		$con[] = 'uid != 0';
		if ($auth && $auth != -1) $con[] = 'authenticated = '.$auth;

		if ($keyword) {
			$con[] = "INSTR(`title`, '{$keyword}') > 0";
		}

		if ($genresAr) {
			$conGenAr = array();
			foreach ($genresAr as $gO) {
				$conGenAr[] = "INSTR(`genres`, '[{$gO}]') > 0";
			}
			$con[] = '('.implode(' OR ', $conGenAr).')';
		}
		if ($authorAr) {
			$conAuAr = array();
			foreach ($authorAr as $aO) {
				$conAuAr[] = "`author` = '{$aO}' ";
			}
			$con[] = '('.implode(' OR ', $conAu).')';
		}

		$con[] = '`show` = 1';

		if ($con) $cond = 'WHERE '.implode(' AND ', $con);

		if (!$order) $order = "modified DESC, created DESC, id DESC";

		$query = "SELECT
					title,link,type,id,authenticated,published,status,thumb,genres,created,modified,uid
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
			$thelink = ($row['authenticated'] == 1) ? $this->bLink : $this->wLink;
			$row['link'] = $thelink.'/'.$row['link'];

			// author
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);
			else $row['author'] = array('name' => $row['author'], 'link' => $this->auLink.'/'.encodeURL($row['author']));

//			$row['des'] = content(substr(htmlspecialchars(strip_tags($row['des'])), 0, 240)).'... <a href="'.$row['link'].'" class="small">Xem đầy đủ</a>';

			// share list
/*			$row['shareNum'] = 0;
			$row['share'] = array();
			if ($row['share']) {
				$shareAr = explode(',', $row['share']);
				$uShare = array();
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
			if ($row['type'] == 0) $row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Đang tiến hành</span>' : '<span class="text-danger">Đã hoàn thành</span>';
			else $row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Mở</span>' : '<span class="text-danger">Khóa</span>';
			$row['sttTextIcon'] = ($row['status'] == 0) ? '<span class="text-success fa fa-refresh"></span>' : '<span class="text-danger fa fa-check hidden"></span>';

			// chapters num
			$row['chaptersNum'] = $this->countChapters($row['id']);

			$this->all_list[] = $row;
		}

		return $stmt;
	}

	function countAll ($auth = -1, $genresAr = null, $authorAr = null, $keyword) {
		$con = array();

		$con[] = 'uid != 0';
		if ($auth && $auth != -1) $con[] = 'authenticated = '.$auth;

		if ($keyword) {
			$con[] = "INSTR(`title`, '{$keyword}') > 0";
		}

		if ($genresAr) {
			$conGenAr = array();
			foreach ($genresAr as $gO) {
				$conGenAr[] = "INSTR(`genres`, '[{$gO}]') > 0";
			}
			$con[] = '('.implode(' OR ', $conGenAr).')';
		}
		if ($authorAr) {
			$conAuAr = array();
			foreach ($authorAr as $aO) {
				$conAuAr[] = "`author` = '{$aO}' ";
			}
			$con[] = '('.implode(' OR ', $conAu).')';
		}

		$con[] = '`show` = 1';

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
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					`show` = 1
					AND (id = ? OR link = ? OR title = ?)
					AND uid != 0
				LIMIT
					0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->id);
		$stmt->bindParam(3, $this->title);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];

		if ($row['id']) {
			$this->link = $row['link'] = $this->wLink.'/'.$row['link'];
			$this->title = $row['title'];
			$this->type = $row['type'];

			// des
			$row['des'] = content($row['des']);

			// is published
			if ($row['published'] == 0) {
				$rq = explode(',', $row['requests']);
				foreach ($rq as $ro)
					$rqAr[] = $this->getUserInfo($ro);
				$row['requests'] = $rqAr;
			}

			// share list
			$row['shareNum'] = 0;
			$row['share'] = array();
			if ($row['share']) {
				$shareAr = explode(',', $row['share']);
				$uShare = array();
				foreach ($shareAr as $oS)
					$uShare[] = $this->getUserInfo($oS);
				$row['share'] = $uShare;
				$row['shareNum'] = count($shareAr);
			}

			// author
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

}
?>
