<?php
class Author extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "authors";

	// object properties
	public $id;
	public $name;
	public $link;
	public $des;
	public $born;
	
	public function __construct() {
		parent::__construct();
		// include simple dom to crawl author data from goodreads and wikipedia if not exists in mBook database
		require_once(MAIN_PATH.'/include/html_dom.php');
	}

	// create product
	function create() {
		//write query
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					name = ?, link = ?, des = ?, born = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->link = encodeURL($this->name);
		$this->des = content($this->des);

		// bind values
		$stmt->bindParam(1, $this->name);
		$stmt->bindParam(2, $this->link);
		$stmt->bindParam(3, $this->des);
		$stmt->bindParam(4, $this->born);

		if ($stmt->execute())
			return true;
		else
			return false;

	}

	function readAll ($order = '') {
		if (!$order) $order = "name ASC, id DESC";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				ORDER BY
					{$order}";

		
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		$this->all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->auLink.'/'.$row['link'];
			
			$this->all_list[] = $row;
		}

		return $stmt;
	}

	function readOne () {
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					id = ? OR link = ? OR name = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->id);
		$stmt->bindParam(3, $this->name);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		
		if ($row['id']) {
			$this->link = $row['link'] = $this->auLink.'/'.$row['link'];
			$this->name = $row['name'];
		}

		return $row;
	}
	
	function works ($order = '') {
		if (!$order) $order = "title ASC, id DESC";

		$query = "SELECT
					id,title,link,thumb
				FROM
					books
				WHERE
					author = ?
				ORDER BY
					{$order}";

		
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->name);
		$stmt->execute();
		
		$bList = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->bLink.'/'.$row['link'];
			
			$bList[] = $row;
		}

		return $bList;
	}


	function update() {

		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					link = :link,
					des = :des,
					born  = :born
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->link = encodeURL($this->name);
		$this->des = htmlspecialchars(strip_tags($this->des));
		$this->id = htmlspecialchars(strip_tags($this->id));

		// bind parameters
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':des', $this->des);
		$stmt->bindParam(':link', $this->link);
		$stmt->bindParam(':born', $this->born);
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
