<?php
class Genre extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "genres";

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
					title = ?, link = ?, des = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->link = encodeURL($this->title);
		$this->des = content($this->des);

		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->link);
		$stmt->bindParam(3, $this->des);

		if ($stmt->execute())
			return true;
		else
			return false;

	}

	function readAll ($order = '') {
		if (!$order) $order = "title ASC, id DESC";

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
			$row['link'] = $this->gnLink.'/'.$row['link'];
			
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
			$this->link = $row['link'] = $this->gnLink.'/'.$row['link'];
			$this->title = $row['title'];
		}

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
