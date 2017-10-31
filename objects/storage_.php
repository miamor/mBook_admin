<?php
class Storage extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "storage";

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

	// create product
	function create() {
		//write query
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					title = ?, link = ?, des = ?, genres = ?, author = ?, uid = ?, published = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->code = htmlspecialchars(strip_tags($this->code));
		$this->des = content($this->des);

		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->link);
		$stmt->bindParam(3, $this->des);
		$stmt->bindParam(4, $this->genres);
		$stmt->bindParam(5, $this->author);
		$stmt->bindParam(6, $this->uid);
		$stmt->bindParam(7, $this->published);

		if ($stmt->execute())
			return true;
		else
			return false;

	}

	function readAll ($genresAr = null, $authorAr = null, $order = '', $keyword = null) {
		$con = array();

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
			$con[] = '('.implode(' OR ', $conAuAr).')';
		}
		$con[] = '`show` = 1';
		if ($con) $cond = 'WHERE '.implode(' AND ', $con);

		if (!$order) $order = "title ASC, created DESC, id DESC";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				{$cond}
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		$this->all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->storageLink.'/'.$row['link'];

			// donator
			$row['author'] = $this->getUserInfo($row['uid']);

			$row['des'] = content(substr(htmlspecialchars(strip_tags($row['des'])), 0, 240)).'... <a href="'.$row['link'].'" class="small">Xem đầy đủ</a>';

			$firstCharacter = encodeURL(strtolower(substr($row['title'], 0, 1)));

			$this->all_list[$firstCharacter][] = $row;
		}

		return $stmt;
	}

	function readOne () {
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					id = ? OR link = ? OR title = ?
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
			$this->link = $row['link'] = $this->storageLink.'/'.$row['link'];
			$this->title = $row['title'];
			$book = $this->getBookInfo($row['bid']);
			$row['bookLink'] = $book['link'];

			// des
			$row['des'] = content($row['des']);

			// donator
			$row['author'] = $this->getUserInfo($row['uid']);
		}

		return $row;
	}

	function getBookInfo ($bid) {
		$query = "SELECT
					link,title
				FROM
					books
				WHERE
					id = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $bid);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['link'] = $this->bLink.'/'.$row['link'];

		return $row;
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
