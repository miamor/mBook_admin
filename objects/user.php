<?php
class User extends Config {

	// database connection and table name
	private $table_name = "members";

	// object properties
	public $id;
	public $title;

	public function __construct() {
		parent::__construct();
	}

	function create () {
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					username = :username,
					first_name = :first_name,
					last_name = :last_name,
					oauth_uid = :oauth_uid,
					coins = :coins,
					avatar = :avatar,
					email = :email,
					type = :type
				";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		$stmt->bindParam(':username', $this->username);
		$stmt->bindParam(':first_name', $this->first_name);
		$stmt->bindParam(':last_name', $this->last_name);
		$stmt->bindParam(':coins', $this->coins);
		$stmt->bindParam(':avatar', $this->avatar);
		$stmt->bindParam(':oauth_uid', $this->oauth_uid);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':type', $this->type);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	function readAll ($limit = '') {
		$lim = '';
		if ($limit) $lim = "LIMIT 0,{$limit}";
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				ORDER BY
					coins DESC, created DESC
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$prevScore = $prevRank = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->uLink.'/'.$row['username'];
			if ($row['type'] == 1) $row['name'] = $row['title'];
			else $row['name'] = ($row['last_name']) ? ($row['last_name'].' '.$row['first_name']) : $row['first_name'];

			$this->uList[] = $row;
		}
		$this->all_list = $this->uList;
		return $this->uList;
	}

	function getAll ($from_record_num = 0, $records_per_page = 24) {
		$lim = '';
		$con = array();

		if ($records_per_page > 0) $lim = "LIMIT {$from_record_num}, {$records_per_page}";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				ORDER BY
					created DESC, coins DESC
				{$lim}";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->uLink.'/'.$row['username'];
			if ($row['type'] == 1) $row['name'] = $row['title'];
			else $row['name'] = ($row['last_name']) ? ($row['last_name'].' '.$row['first_name']) : $row['first_name'];

			$this->uList[] = $row;
		}
		//$this->all_list = $this->uList;
		return $this->uList;
	}

	function countAll () {
		$query = "SELECT
					id
				FROM
					" . $this->table_name . "
				";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		return $stmt->rowCount();
	}

	function readOne ($withC = false) {
		$cond = '';

		$query = "SELECT * FROM " . $this->table_name . " WHERE id = ? OR username = ? limit 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->id);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$row['link'] = $this->uLink.'/'.$row['username'];
		if ($row['type'] == 1) $row['name'] = $row['title'];
		else $row['name'] = ($row['last_name']) ? ($row['last_name'].' '.$row['first_name']) : $row['first_name'];

		$this->username = $row['username'];
		$this->id = (int)$row['id'];
		$this->name = $row['name'];
		$this->avatar = $row['avatar'];
		$this->rank = $row['rank'];
		$this->link= $row['link'];

		// followers, following
		if ($row['followers']) {
			preg_match_all("/\[(.*?)\]/", $row['followers'], $matches);
			$this->followers = $row['followers'] = $matches[1];
		} else $row['followers'] = array();
		if ($row['followings']) {
			preg_match_all("/\[(.*?)\]/", $row['followings'], $matches);
			$this->followings = $row['followings'] = $matches[1];
		} else $row['followings'] = array();
		$this->followersNum = $row['followersNum'] = count($row['followers']);
		$this->followingsNum = $row['followingsNum'] = count($row['followings']);

		return $row;
	}

	function sReadOne ($u = null) {
		if (!$u) $u = $this->u;

		$query = "SELECT username,avatar,first_name,last_name,online FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $u);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['username']) {
			$row['link'] = $this->uLink.'/'.$row['username'];
			$row['name'] = ($row['last_name']) ? ($row['last_name'].' '.$row['first_name']) : $row['first_name'];
		}

		return $row;
	}

	function sReadOneByUsername () {
		$query = "SELECT id FROM " . $this->table_name . " WHERE username = ? LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->username);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	public function countSubmissions ($u) {
		if ($u) $cond = "WHERE uid = {$u}";

		$query = "SELECT id FROM submissions {$cond}";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$num = $stmt->rowCount();
		return $num;
	}

	function updateRankings () {
		$query = "SELECT rank,score,id FROM " . $this->table_name." ORDER BY score DESC";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$k = 0;
		$prevScore = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($prevScore != $row['score']) $k++;
			$prevScore = $row['score'];
			$updateRank = $this->editUserData(array('rank' => $k), $row['id']);
		}

		if ($updateRank) return true;
	}

	function editUserData ($valueAr, $u) {
		if (!$u) $u = $this->id;

		$condAr = array();
		foreach ($valueAr as $vK => $oneField)
			$condAr[] = "{$vK} = {$oneField}";
		$cond = implode(', ', $condAr);

		$query = "UPDATE
					" . $this->table_name . "
				SET
					{$cond}
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		// bind parameters
		/*foreach ($valueAr as $vK => $oneVal) {
			$stmt->bindParam(':'.$vk, $oneVal);
		}*/
		$stmt->bindParam(':id', $u);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	public function update() {
		$query = "UPDATE
					" . $this->table_name . "
				SET
					username = :username,
					first_name = :first_name,
					last_name = :last_name,
					oauth_uid = :oauth_uid,
					coins = :coins,
					avatar = :avatar,
					email = :email,
					type = :type
				WHERE
					id = :id";

		$stmt = $this->conn->prepare($query);

		//echo $this->username.'~'.$this->first_name.'~'.$this->last_name.'~'.$this->coins.'~'.$this->avatar.'~'.$this->oauth_uid.'~'.$this->email.'~'.$this->id;
		// bind parameters
		$stmt->bindParam(':username', $this->username);
		$stmt->bindParam(':first_name', $this->first_name);
		$stmt->bindParam(':last_name', $this->last_name);
		$stmt->bindParam(':coins', $this->coins);
		$stmt->bindParam(':avatar', $this->avatar);
		$stmt->bindParam(':oauth_uid', $this->oauth_uid);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':type', $this->type);
		$stmt->bindParam(':id', $this->id);

		// execute the query
		if ($stmt->execute()) return true;
		else return false;
	}

	public function delete() {
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		if ($result = $stmt->execute()) return true;
		else return false;
	}

}
?>
