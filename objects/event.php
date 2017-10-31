<?php
class Event extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "events";

	public function __construct() {
		parent::__construct();
	}

	function readAll ($order = '') {
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE 
					start_time >= NOW()
						OR
					end_time >= NOW()
				ORDER BY
					start_time ASC ";

		
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		$this->all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->eLink.'/'.$row['id'];
			$start = strtotime($row['start_time']);
			$row['start']['day'] = date('d',$start);
			$row['start']['month'] = date('m',$start);
			$row['start']['year'] = date('Y',$start);
			$row['place'] = json_decode($row['place'], true);
			$this->all_list[] = $row;
		}

		return $this->all_list;
	}

	function readOne () {
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					id = ? OR title = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->title);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		
		if ($row['id']) {
			$this->link = $row['link'] = $this->eLink.'/'.$row['id'];
			$this->title = $row['title'];
		}

		return $row;
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
