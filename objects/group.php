<?php
class Group extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "groups";

	public function __construct() {
		parent::__construct();
	}

	// create product
	function create() {
		//write query
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					title = ?, code = ?, des = ?, genres = ?, author = ?, uid = ?, published = ?";

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

	function checkMyJoin () {
		return in_array($this->u, $this->members);
	}

	function join () {
		$members = $this->members_txt.' ['.$this->u.']';
		$this->members_txt = str_replace(' ', ',', trim($members));
		$this->members[] = $this->u;

		$query = "UPDATE
					" . $this->table_name . "
				SET
					members  = :members
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':members', $this->members_txt);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	function leave () {
		$members_txt = str_replace("[{$this->u}]", '', $this->members_txt);
		$this->members_txt = str_replace(",,", ',', $members_txt);
		if (($key = array_search($this->u, $this->members)) !== false) {
			unset($this->members[$key]);
		}

		$query = "UPDATE
					" . $this->table_name . "
				SET
					members  = :members
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':members', $this->members_txt);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	function readAll ($order = '', $from_record_num = 0, $records_per_page = 10) {
		$lim = '';
		$con = array();
		if ($from_record_num) $lim = "LIMIT
					{$from_record_num}, {$records_per_page}";

		if (!$order) $order = "modified DESC, created DESC, id DESC";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					status != 2
				ORDER BY
					{$order}
				{$lim}";


		$stmt = $this->conn->prepare($query);

		$stmt->execute();

		$this->all_list = array();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->grLink.'/'.$row['code'];
			// creator
			$row['creator'] = $this->getUserInfo($row['uid']);

			$row['des'] = content(substr(htmlspecialchars(strip_tags($row['des'])), 0, 240)).'... <a href="'.$row['link'].'" class="small">Xem đầy đủ</a>';

			// members list
			$row['memNum'] = 0;
			$row['memAr'] = array();
			if ($row['members']) {
				$memAr = explode(',', $row['members']);
				$row['memAr'] = $uMem;
				$row['memNum'] = count($memAr);
			}

			// status
			if ($row['status'] == 2) $row['sttText'] = '<span class="text-danger">Bí mật</span>';
			else if ($row['status'] == 1) $row['sttText'] = '<span class="text-warning">Đóng</span>';
			else $row['sttText'] = '<span class="text-success">Mở</span>';

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
					id = ? OR code = ? OR title = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->id);
		$stmt->bindParam(3, $this->title);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		$this->members = array();
		$this->isMember = null;

		if ($row['id']) {
			$this->link = $row['link'] = $this->grLink.'/'.$row['code'];
			$this->title = $row['title'];

			// des
			$row['des'] = content($row['des']);

			// creator
			$row['creator'] = $this->getUserInfo($row['uid']);

			// members list
			$row['members_txt'] = $this->members_txt = $row['members'];
			$row['memNum'] = 0;
			$row['memAr'] = array();
			if ($row['members']) {
				$memAr = explode(',', $row['members']);
				$uMem = array();
				foreach ($memAr as $oS) {
					$oU = intval(preg_replace('/[^0-9]+/', '', $oS), 10);
					$mems[] = $oU;
					$uMem[] = $this->getUserInfo($oU);
				}
				$row['members'] = $this->members = $mems;
				$row['memAr'] = $uMem;
				$row['memNum'] = count($memAr);
			}
			$this->memAr = $row['memAr'];
			$this->isMember = $this->checkMyJoin();

			// status
			if ($row['status'] == 2) $row['sttText'] = '<span class="text-danger">Bí mật</span>';
			else if ($row['status'] == 1) $row['sttText'] = '<span class="text-warning">Đóng</span>';
			else $row['sttText'] = '<span class="text-success">Mở</span>';
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
