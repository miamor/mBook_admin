<?php
class Notification extends Config {

	// database connection and table name
	private $table_name = "notification";

	// object properties
	public $id;
	public $title;

	public function __construct() {
		parent::__construct();
	}

	function readAll ($page = null) {
		if (!$page) $lim = '';
		else {
			$numPerPage = 5;
			$start = $page*$numPerPage;
			$end = $start+$numPerPage;
			$lim = "LIMIT {$start},{$end}";
		}
//				GROUP BY type,post_id
//				GROUP BY type,iid
		$query = "SELECT
					*
				FROM " . $this->table_name . "
				WHERE uid = ?
				ORDER BY id DESC
				{$lim}";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->u);
		$stmt->execute();
		$this->all_list = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row['from_uid']) $row['author'] = $this->sGetUserInfo($row['from_uid']);
			else $row['author'] = array();
			$row['content'] = json_decode($row['content'], true);
//			$row['content']['content'] = substr(htmlspecialchars(strip_tags($row['content']['content'])), 0, 500);
			if (strlen($row['content']['content']) > 40) $row['content']['content'] = substr($row['content']['content'], 0, 40).'...';
			$this->all_list[] = $row;
		}
		return $this->all_list;
	}

	function readOne () {
		$query = "SELECT
					*
				FROM " . $this->table_name . "
				WHERE id = ? 
				LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->id = $row['id'];
		if ($row['id']) {
			$this->link = $row['link'] = $this->storageLink.'/'.$row['link'];
			$row['author'] = $this->sGetUserInfo($row['from_uid']);
			$row['content'] = json_decode($row['content'], true);
		}
		return $row;
	}

	function getBookChapter ($iid) {
		$query = "SELECT
					bid,cid
				FROM books_chapters_reviews
				WHERE id = ? 
				LIMIT 0,1";
		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $iid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function setRead () {
		$query = "UPDATE
					" . $this->table_name . "
				SET
					is_new  = 0
				WHERE
					id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id', $this->id);
		if ($stmt->execute()) return true; 
		else return false;
	}

}
?>
