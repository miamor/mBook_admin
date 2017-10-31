<?php
class Page extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "members";

	public function __construct() {
		parent::__construct();
	}

	function insert () {
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					title = ?, oauth_token = ?, oauth_uid = ?, category = ?, uid = ?, avatar = ?, cover = ?, type = 1, username = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->name);
		$stmt->bindParam(2, $this->fb_token);
		$stmt->bindParam(3, $this->fb_id);
		$stmt->bindParam(4, $this->category);
		$stmt->bindParam(5, $this->u);
		$stmt->bindParam(6, $this->avatar);
		$stmt->bindParam(7, $this->cover);
		$stmt->bindParam(8, $this->fb_id);

		if ($stmt->execute()) {
			return true;
		} else {
			return false;
		}
	}

	function checkImport ($fb_id) {
		$query = "SELECT id FROM
					" . $this->table_name . "
				WHERE
					oauth_uid = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $fb_id);
		$stmt->execute();
		return ($stmt->rowCount()) ? true : false;
	}

	function update () {

		$query = "UPDATE
					" . $this->table_name . "
				SET
					title = :title,
					avatar = :avatar,
					cover = :cover
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		$this->title=htmlspecialchars(strip_tags($this->title));
		$this->avatar=htmlspecialchars(strip_tags($this->avatar));
		$this->cover=htmlspecialchars(strip_tags($this->cover));

		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':avatar', $this->avatar);
		$stmt->bindParam(':cover', $this->cover);
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
