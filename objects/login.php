<?php
class Login extends Config {

	// database connection and table name
	private $table_name = "members";

	// object properties
	public $id;
	public $title;

	public function __construct() {
		parent::__construct();
	}

	function loginFb () {
		$query = "SELECT oauth_uid,oauth_token,id FROM " . $this->table_name . " WHERE oauth_uid = ? OR username = ? LIMIT 0, 1";	

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->fb_uid);
		$stmt->bindParam(2, $this->username);
		$stmt->execute();
		$num = $stmt->rowCount();

		if ($num > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
//			$this->u = $row['id'];
			if ($row['oauth_uid']) {
				$query = "UPDATE " . $this->table_name . " SET oauth_token = :token WHERE oauth_uid = :uid";

				$stmt = $this->conn->prepare($query);
				
				$stmt->bindParam(':token', $this->fb_token);
				$stmt->bindParam(':uid', $this->fb_uid);
				
				// execute the query
				if ($stmt->execute()) {
					// get id of this user
					$this->getUserID();
					return true;
				}
				else return false;
			}
		} 
		else {
			$query = "INSERT INTO
						members
					SET
						oauth_uid = ?, oauth_token = ?, first_name = ?, last_name = ?, username = ?, avatar = ?";

			$stmt = $this->conn->prepare($query);
		
			$name = explode(' ', $this->name);
			$fname = $name[count($name) - 1];
			$lname = $name[0];
			// bind values
			$stmt->bindParam(1, $this->fb_uid);
			$stmt->bindParam(2, $this->fb_token);
			$stmt->bindParam(3, $fname);
			$stmt->bindParam(4, $lname);
			$stmt->bindParam(5, $this->username);
			$stmt->bindParam(6, $this->avatar);

			// execute the query
			if ($stmt->execute()) {
				// get id of this user
				$this->getUserID();
				return true;
			}
			else return true;
		}
		return false;
	}

	function getUserID () {
		$query = "SELECT oauth_uid,id FROM " . $this->table_name . " WHERE oauth_uid = ? LIMIT 0, 1";	
		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->fb_uid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount()) {
			$this->id = $row['id'];
			return $row['id'];
		} else return false;
	}

	function getUserToken () {
		$query = "SELECT oauth_token FROM " . $this->table_name . " WHERE oauth_uid = ? LIMIT 0, 1";	
		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->fb_uid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount()) {
			$this->fb_token = $row['oauth_token'];
			return $row['oauth_token'];
		} else return false;
	}

	function login () {
		$query = "SELECT username,password,id FROM " . $this->table_name . " WHERE username = ? AND password = ? LIMIT 0, 1";	

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->username);
		$stmt->bindParam(2, $this->password);
		$stmt->execute();
		$num = $stmt->rowCount();

		if ($num > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->uid = $row['id'];
			
			// update online
			$query = "UPDATE
					" . $this->table_name . "
				SET
					online = 1
				WHERE
					id = :id";

			$stmt = $this->conn->prepare($query);
			
			$stmt->bindParam(':id', $this->uid);

			// execute the query
			if ($stmt->execute()) return true;
			else return true;
		} 
		
		return false;
	}

	function readOne ($withC = false) {
		$cond = '';
		
		$query = "SELECT * FROM " . $this->table_name . " WHERE id = ? OR username = ? limit 0,1";

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->id);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$this->username = $row['username'];
		$this->id = $row['id'];

		$row['link'] = $this->uLink.'/'.$row['username'];
		$row['name'] = ($row['last_name']) ? ($row['last_name'].' '.$row['first_name']) : $row['first_name'];

		return $row;
	}

	function logout () {
		$query = "SELECT id FROM " . $this->table_name . " WHERE id = ? LIMIT 0, 1";	

		$stmt = $this->conn->prepare( $query );
		$stmt->bindParam(1, $this->u);
		$stmt->execute();
		$num = $stmt->rowCount();

		if ($num > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			// update online
			$query = "UPDATE
					" . $this->table_name . "
				SET
					online = 0,
				WHERE
					id = :id";

			$stmt = $this->conn->prepare($query);
			
			$stmt->bindParam(':id', $this->u);

			// execute the query
			if ($stmt->execute()) return true;
			else return true;
		} 
		
		return false;
	}


}
?>
