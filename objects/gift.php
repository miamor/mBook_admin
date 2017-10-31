<?php
class Gift extends Config {
	private $table_name = "gifts";

	
	public function __construct() {
		parent::__construct();
	}

	function make ($row) {
		$row['link'] = $this->gLink.'/'.$row['id'];
		$row['author'] = $this->getUserInfo($row['uid']);

		// content 
		$row['content'] = content($row['content']);

		// share list
		$row['shareNum'] = 0;
		$row['share'] = array();
		if ($$row['share']) {
			$shareAr = explode(',', $row['share']);
			foreach ($shareAr as $oS) 
				$uShare[] = $this->getUserInfo($oS);
			$row['share'] = $uShare;
			$row['shareNum'] = count($shareAr);
		}
		
		return $row;
	}
	
	function readAll ($iid) {
		if (!$order) $order = "modified DESC, created DESC, id DESC";
		if (!$iid) $iid = $this->iid;
		
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);

		$stmt->execute();

		$this->gList = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row = $this->make($row);
			
			$this->gList[] = $row;
		}

		return $stmt;
	}

	function readOne () {
		if (!$order) $order = "modified DESC, created DESC, id DESC";
		
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE 
					id = ?
				ORDER BY
					{$order}
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->id = $row['id'];
		if ($row['id']) {
			$row = $this->make($row);

/*			$book = new Book();
			$book->id = $row['iid'];
*/			$row['book'] = $this->getBookInfo($row['iid']);
		
			// share list
			$row['shareNum'] = 0;
			$row['share'] = array();
			if ($$row['share']) {
				$shareAr = explode(',', $row['share']);
				foreach ($shareAr as $oS) 
					$uShare[] = $this->getUserInfo($oS);
				$row['share'] = $uShare;
				$row['shareNum'] = count($shareAr);
			}

			// comments
			$row['ratingsList'] = $this->getComments();
			$row['ratingsNum'] = count($row['ratingsList']);

		}

		return $row;
	}


	function getComments ($id) {
		if (!$order) $order = "modified DESC, created DESC, id DESC";
		if (!$id) $id = $this->id;
		
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_comments
				WHERE 
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$cmtList = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['author'] = $this->getUserInfo($row['uid']);
			
			$cmtList[] = $row;
		}

		return $cmtList;
	}

	
	function getBookInfo ($id) {
		$query = "SELECT
					thumb,title,link,uid,published,status,author,genres
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
			
			// des
			$row['des'] = content($row['des']);

			// author
			if ($row['uid']) $row['author'] = $this->getUserInfo($row['uid']);
			else $row['author'] = array('name' => $row['author'], 'link' => $this->auLink.'/'.encodeURL($row['author']));
			
			// genres
			$gnr = explode(',', $row['genres']);
			$gAr = $gTxtAr = array();
			foreach ($gnr as $gno) {
				$gIn = $this->getBookGenre($gno);
				$gAr[] = $gIn;
				$gTxtAr[] = '<a href="'.$gIn['link'].'">'.$gIn['title'].'</a>';
			}
			$row['genres'] = $gAr;
			$row['genresText'] = implode(', ', $gTxtAr);

			// ratings
			$this->getBookReviews($id);
			$row['averageRate'] = $this->bAverage;
			$row['totalReview'] = $this->bTotal;
			
			// status
			if ($row['type'] == 0) $row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Đang tiến hành</span>' : '<span class="text-danger">Đã hoàn thành</span>';
			else $row['sttText'] = ($row['status'] == 0) ? '<span class="text-success">Mở</span>' : '<span class="text-danger">Khóa</span>';
		}

		return $row;
	}
	
	function getBookGenre ($g) {
		$query = "SELECT
					title,link,id
				FROM
					genres
				WHERE
					id = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $g);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['link'] = $this->gnLink.'/'.$row['link'];
		
		return $row;
	}
	
	function getBookReviews ($iid) {
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

}