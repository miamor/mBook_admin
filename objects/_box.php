<?php
class Box extends Config {

	private $pHash;
	private $table_name = "boxes";

	public function __construct() {
		parent::__construct();
		require_once(MAIN_PATH.'/include/phash.class.php');
		
		$this->pHash = pHash::Instance();
	}
	
	function add () {
		$query = "INSERT INTO
					" . $this->table_name . "_books
				SET
					title = ?, location = ?, rows = ?, cols = ?, squares = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->location = htmlspecialchars(strip_tags($this->location));
		$this->squares = $this->rows * $this->cols;

		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->location);
		$stmt->bindParam(3, $this->rows);
		$stmt->bindParam(4, $this->cols);
		$stmt->bindParam(5, $this->squares);

		if ($stmt->execute()) {
			if ($this->addPost('addbox')) return true;
			else return false;
		} else {
			return false;
		}
	}
	
	function getBookInfo ($id) {
		$query = "SELECT
					id,thumb,title,link,uid,published,status,author,genres
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
		}
		return $row;
	}
	
	function readOne () {
		$query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row['id']) {
			$this->id = $row['id'];
			$this->title = $row['title'];
			$this->location = $row['location'];
			$this->thumb = $row['thumb'];
			$this->stt = $row['stt'];
			$row['link'] = $this->link = $this->boxLink.'/'.$row['id'];
//			$this->countBooks($row['id']);
			$this->getBooksList();
			$row['booksList'] = $this->booksList;
			$row['books_title_num'] = $this->booksTitleNum;
			$row['books_num'] = $this->booksNum;
			$row['widthPerSquare'] = 100/$row['cols'];
			return $row;
		}
		return false;
	}

	function readAll () {
		$query = "SELECT * FROM " . $this->table_name ;
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->id = $row['id'];
			$this->boxesList = array();
			if ($row['id']) {
				$row['link'] = $this->boxLink.'/'.$row['id'];
				$this->countBooks($row['id']);
				$row['books_title_num'] = $this->booksTitleNum;
				$row['books_num'] = $this->booksNum;
				$this->boxesList[] = $row;
			}
		}
		$this->id = $this->booksTitleNum = $this->booksNum = null;
		return $this->boxesList;
	}

	function countBooks ($id = null) {
		if (!$id) $id = $this->id;
		$query = "SELECT num FROM " . $this->table_name ."_books WHERE box_id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$this->booksNum = $this->booksTitleNum = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->booksTitleNum++;
			$this->booksNum += $row['num'];
		}
	}

	function getBooksList () {
		$query = "SELECT
					*
				FROM
					".$this->table_name."_books
				WHERE
					box_id = ?
				ORDER BY id DESC";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		$this->booksNum = $this->booksTitleNum = 0;
		$this->booksList = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->booksTitleNum++;
			$this->booksNum += $row['num'];
			$this->booksList[] = $row;
		}
	}

	function readOneBook ($id = null) {
		if (!$id) $id = $this->book_id;
		$query = "SELECT
					*
				FROM
					".$this->table_name."_books
				WHERE
					id = ? AND box_id = ?
				ORDER BY id DESC
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->bindParam(2, $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->book_title = $row['title'];
		$this->book_link = $row['link'];
		$this->book_barcode = $row['barcode'];
		$this->book_coverIMG = $row['cover'];
		$this->book_backIMG = $row['back'];
		$this->book_price = $row['price'];
		$this->book_price_rent = $row['price_rent'];
		$this->book_num = $row['num'];
		$this->book_bid = $row['bid'];
		$this->book_square_id = $row['square_id'];
		return $row;
	}

	function edit () {
		$query = "UPDATE
					" . $this->table_name . "
				SET
					title = ?, location = ?, thumb = ?
				WHERE id = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->location = htmlspecialchars(strip_tags($this->location));
		$this->thumb = htmlspecialchars(strip_tags($this->thumb));

		// bind values
		$stmt->bindParam(1, $this->title);
		$stmt->bindParam(2, $this->location);
		$stmt->bindParam(3, $this->thumb);
		$stmt->bindParam(4, $this->id);

		if ($stmt->execute()) {
			if ($this->addPost()) return true;
			else return false;
		} else {
			return false;
		}
	}
	
	function lock () {
		$stt = (!$this->stt) ? 1 : 0;
		$query = "UPDATE
					" . $this->table_name . "
				SET
					stt = ?
				WHERE id = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $stt);
		$stmt->bindParam(2, $this->id);

		if ($stmt->execute()) {
			$this->stt = $stt;
			return true;
		} else 
			return false;
	}

	function resize ($file, $w = 800, $h = 800, $crop = false) {
		list($width, $height) = getimagesize($file);
		$r = $width / $height;
		if ($crop) {
			if ($width > $height) {
				$width = ceil($width-($width*abs($r-$w/$h)));
			} else {
				$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		} else {
			if ($w/$h > $r) {
				$newwidth = $h*$r;
				$newheight = $h;
			} else {
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
//		echo '{'.$width.' - '.$height.' - '.$r.' @ '.$newwidth.' - '.$newheight.'}';
		$src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		// Rotate
/*		if ($width > $height) {
			$bgColor = imagecolorallocatealpha($dst, 255, 255, 255, 127);
			$dst = imagerotate($dst, -90);
//			$rotate = imagerotate($src, 90, $bgColor);
		}
*/
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		// save to output
/*		$ext = end(explode('.', $file));
		$name = explode('.'.$ext, $file)[0].'_resize';
		$outputFile = $name.'.'.$ext;
*/		$outputFile = $file;
		imagejpeg($dst, $outputFile);
		return $outputFile;
	}

	function upload ($file, $isFront = true) {
		$ar = explode('.', $file['name']);
		$ext = end($ar);

		$name = explode('.'.$ext, $file['name'])[0];
		if (!$isFront) $name .= '_back';
		$randomCode = generateRandomString();
		$fname = "{$name}_{$randomCode}.{$ext}";
		$new_path = MAIN_PATH.'/data/img/books/'.$fname;
		if ($file['error'] > 0) {
			echo 'File upload error: '.$file['error'];
			return false;
		} else {
			move_uploaded_file($file['tmp_name'], $new_path);
		}
		return $new_path;
	}

	function updateCoverCodeFile () {
		$dataFile = MAIN_PATH.'/data/books.txt';
		// search this book cover code in data file
		if (!exec('grep '.escapeshellarg($this->book_coverCode).' '.$dataFile)) {
			// if not existed;
			file_put_contents($dataFile, $this->book_coverCode.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
		return true;
	}

	function addBook ($id = null) {
		if (!$id) $id = $this->id;
		$title = $this->book_title;
		$code = $this->book_link = encodeURL($title);
		
		/* back */
		$img = $this->resize($this->book_backIMG);
		$barcode = $this->book_barcode;
		$link = encodeURL($title);

		/* cover */
		$img = $this->resize($this->book_coverIMG);
		// get pHash
		//$hash = $this->HashImage($img);
		$pixels = $this->pHash->getPixImage($img);
		$pixStr = implode(',', $pixels);

		// generate code
		$this->book_coverCode = $pixStr.','.$code;
		// update cover code data file
		$this->updateCoverCodeFile();

		/* add */
		$query = "INSERT INTO
					" . $this->table_name . "_books
				SET
					title = ?, link = ?, cover = ?, back =?, barcode = ?, cover_code = ?, price = ?, price_rent = ?, box_id = ?, num = ?, bid = ?, square_id = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
		$this->book_title = htmlspecialchars(strip_tags($this->book_title));
		$this->book_coverIMG = str_replace(MAIN_PATH, MAIN_URL, $this->book_coverIMG);
		$this->book_backIMG = str_replace(MAIN_PATH, MAIN_URL, $this->book_backIMG);

		// bind values
		$stmt->bindParam(1, $this->book_title);
		$stmt->bindParam(2, $code);
		$stmt->bindParam(3, $this->book_coverIMG);
		$stmt->bindParam(4, $this->book_backIMG);
		$stmt->bindParam(5, $this->book_barcode);
		$stmt->bindParam(6, $this->book_coverCode);
		$stmt->bindParam(7, $this->book_price);
		$stmt->bindParam(8, $this->book_price_rent);
		$stmt->bindParam(9, $this->id);
		$stmt->bindParam(10, $this->book_num);
		$stmt->bindParam(11, $this->book_bid);
		$stmt->bindParam(12, $this->book_square_id);

		if ($stmt->execute()) {
			if ($this->addPost('addbookbox')) return true;
			else return false;
		} else {
			return false;
		}
	}
	
	protected function getLastBookInBookBox ($id = null) {
		if (!$id) $id = $this->id;
		$query = "SELECT id FROM ".$this->table_name."_books WHERE box_id = ? ORDER BY id DESC LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['id'];
	}
	protected function getLastBox () {
		if (!$id) $id = $this->id;
		$query = "SELECT id FROM ".$this->table_name." ORDER BY id DESC LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['id'];
	}

	protected function addPost ($type) {
		if ($type == 'addbox') {
			$iid = $this->id = $this->getLastBox();
			$query = "INSERT INTO posts SET
						type = ?, iid = ?";

			$stmt = $this->conn->prepare($query);
			// bind values
			$stmt->bindParam(1, $type);
			$stmt->bindParam(2, $iid);

			if ($stmt->execute()) {
				return true;
			} else return false;
		}
		
		if ($type == 'addbookbox') {
			$iid = $this->getLastBookInBookBox();
			$query = "INSERT INTO posts SET
						type = ?, iid = ?, bid = ?";

			$stmt = $this->conn->prepare($query);
			// bind values
			$stmt->bindParam(1, $type);
			$stmt->bindParam(2, $iid);
			$stmt->bindParam(3, $this->id);

			if ($stmt->execute()) {
				return true;
			} else return false;
		}

		if ($type == 'buy') {
			$query = "INSERT INTO posts SET
						type = ?, iid = ?, bid = ?";

			$stmt = $this->conn->prepare($query);
			// bind values
			$stmt->bindParam(1, $type);
			$stmt->bindParam(2, $this->book_id);
			$stmt->bindParam(3, $this->id);

			if ($stmt->execute()) return true;
			else return false;
		}
	}

	function returnBook () {
		/* cover */
		$img = $this->resize($this->book_coverIMG);
		// get pHash
		//$hash = $this->HashImage($img);
		$pixels = $this->pHash->getPixImage($img);
		$pixStr = implode(',', $pixels);

		// generate code
		$this->book_coverCodePix = $pixStr;
		
		echo $this->book_coverCodePix.'<hr/>';
		
		// find book title using cover code
		$dataFile = MAIN_PATH.'/data/books.txt';
		$pyFile = MAIN_PATH.'/include/books.py';
		// search this book cover code in data file
		$out = exec('python');
		echo $out.'<hr/>';
		
/*		$this->book_coverCode = $this->book_coverCodePix.','.$code;
		
		// finish search, detect book link => update to dataFile
		// if this code not existed
		if (!exec('grep '.escapeshellarg($this->book_coverCode).' '.$dataFile)) {
			file_put_contents($dataFile, $this->book_coverCode.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
*/
	}

	function editBook ($id = null) {
		if (!$id) $id = $this->book_id;
		$title = $this->book_title;
		$code = $this->book_link = encodeURL($title);
		
		/* back */
		if ($this->book_backIMG) {
			$img = $this->resize($this->book_backIMG);
			$barcode = $this->book_barcode;
			$link = encodeURL($title);
		}

		/* cover */
		if ($this->book_coverIMG) {
			$img = $this->resize($this->book_coverIMG);
			// get pHash
			//$hash = $this->HashImage($img);
			$pixels = $this->pHash->getPixImage($img);
			$pixStr = implode(',', $pixels);

			// generate code
			$this->book_coverCode = $pixStr.','.$code;
			// update cover code data file
			$this->updateCoverCodeFile();
		}
		
		/* add */
		$query = "UPDATE
					" . $this->table_name . "_books
				SET";
		if ($this->book_coverIMG && $this->book_backIMG)
			$query .= "
					title = ?, link =?, barcode = ?, price = ?, price_rent = ?, box_id = ?, num = ?, bid = ?, square_id = ?, cover = ?, cover_code = ?, back = ?";
		else if ($this->book_coverIMG)
			$query .= "
					title = ?, link =?, barcode = ?, price = ?, price_rent = ?, box_id = ?, num = ?, bid = ?, square_id = ?, cover = ?, cover_code = ?";
		else if ($this->book_backIMG)
			$query .= "
					title = ?, link =?, barcode = ?, price = ?, price_rent = ?, box_id = ?, num = ?, bid = ?, square_id = ?, back = ?";
		else 
			$query .= "
					title = ?, link =?, barcode = ?, price = ?, price_rent = ?, box_id = ?, num = ?, bid = ?, square_id = ?";
		
		$query .= " WHERE id = {$id}";
		
		$stmt = $this->conn->prepare($query);

		// posted values
		$this->book_title = htmlspecialchars(strip_tags($this->book_title));
		if ($this->book_coverIMG) $this->book_coverIMG = str_replace(MAIN_PATH, MAIN_URL, $this->book_coverIMG);
		if ($this->book_backIMG) $this->book_backIMG = str_replace(MAIN_PATH, MAIN_URL, $this->book_backIMG);

		// bind values
		$stmt->bindParam(1, $this->book_title);
		$stmt->bindParam(2, $code);
		$stmt->bindParam(3, $this->book_barcode);
		$stmt->bindParam(4, $this->book_price);
		$stmt->bindParam(5, $this->book_price_rent);
		$stmt->bindParam(6, $this->id);
		$stmt->bindParam(7, $this->book_num);
		$stmt->bindParam(8, $this->book_bid);
		$stmt->bindParam(9, $this->book_square_id);
		if ($this->book_coverIMG && $this->book_backIMG) {
			$stmt->bindParam(10, $this->book_coverIMG);
			$stmt->bindParam(11, $this->book_coverCode);
			$stmt->bindParam(12, $this->book_backIMG);
		}
		else if ($this->book_coverIMG) {
			$stmt->bindParam(10, $this->book_coverIMG);
			$stmt->bindParam(11, $this->book_coverCode);
		}
		else if ($this->book_backIMG) {
			$stmt->bindParam(10, $this->book_backIMG);
		}

		if ($stmt->execute()) {
			return true;
		} else {
			return false;
		}
	}
	

	function searchBarcode () {
		// barcode is EN13
		// 8 936024 917401
		// 1-6: factory code
		$factoryCode = substr($this->barcode, 1, 6);
		$itemCode = substr($this->barcode, 7, 12);
		// read barcode data from factoryCode.txt
		$file = MAIN_PATH."/data/{$factoryCode}.txt";
		$contents = file_get_contents($file);
		preg_match("/\[{$itemCode}\]\{(.*)\}\<(.*)\>/m", $contents, $match);
		$this->bookLink = $match[1];
		$this->edition = $match[2];
	}

	function searchCover () {
	}

	function checkIfDone () {
/*		$query = "SELECT id,done,square_id FROM
					" . $this->table_name . "_books_buy
				WHERE box_id = ? AND iid = ?
				ORDER BY id DESC
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->book_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row['id']) {
			$row['done'] = -1;
		} else if ($row['done'] == 1) {
			$this->decreaseBookNum();
			// substract coins
			
		}
		return $row;
*/
		if (file_get_contents(MAIN_PATH.'/data/box/'.$this->id.'.json') == 0) echo 1;
		else echo 0;
	}

/*	function changeLastInProgress_ToDone () {
		$query = "UPDATE
					" . $this->table_name . "_books_buy
				SET done = 1 
				WHERE box_id = ? 
				ORDER BY id DESC
				LIMIT 1";
		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->id);

		if ($stmt->execute()) {
			file_put_contents(MAIN_PATH.'/data/box/'.$this->id.'.json', 0);
			return true;
		} else 
			return false;
	}
*/
	function changeHandlingRequest_ToDone () {
	}
	
/*	function lastInProgress () {
		$query = "SELECT id,done,square_id FROM
					" . $this->table_name . "_books_buy
				WHERE box_id = ? AND done = 0
				ORDER BY id DESC
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->id);
		$stmt->execute();

		if (!$stmt->rowCount()) {
			$row['square_id'] = 0;
		} else $row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function lastBuy () {
		$query = "SELECT id,iid,done FROM
					" . $this->table_name . "_books_buy
				WHERE box_id = ?
				ORDER BY id DESC
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row['id']) {
			$row['id'] = 0;
			$row['done'] = 1;
		}
		$this->lastBuy = $row;
		return $row;
	}
*/
	function checkInProgress () {
		$query = "SELECT id,done,square_id FROM
					" . $this->table_name . "_books_buy
				WHERE box_id = ? AND done = 0 AND square_id = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		// bind values
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->book_square_id);
		$stmt->execute();
		return $stmt->rowCount();
	}

	function checkMyTurn () {
		$query = "SELECT id,done,square_id FROM
					" . $this->table_name . "_books_buy
				WHERE box_id = ? AND done = 0
				ORDER BY id ASC
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		// bind values
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->book_square_id);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row['square_id'] == $this->book_square_id) return true;
			else return false;
		}
		else return true;
	}

	function requestBuy () {
			$query = "INSERT INTO
						" . $this->table_name . "_books_buy
					SET
						uid = ?, box_id = ?, iid = ?, bid = ?, ilink = ?, square_id = ?";

			$stmt = $this->conn->prepare($query);

			// bind values
			$stmt->bindParam(1, $this->u);
			$stmt->bindParam(2, $this->id);
			$stmt->bindParam(3, $this->book_id);
			$stmt->bindParam(4, $this->book_bid);
			$stmt->bindParam(5, $this->book_link);
			$stmt->bindParam(6, $this->book_square_id);

			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
	}
	
	function handleBuy () {
		if ($this->checkMyTurn()) { // is my turn to handle
			$this->changeHandlingRequest_ToDone();
		}
	}
	
	function finishBuy () {
				// decrease book number
				$this->decreaseBookNum();
				file_put_contents(MAIN_PATH.'/data/box/'.$this->id.'.json', $this->book_square_id);
				if ($this->addPost('buy')) return true;
				else return false;
	}

/*	function buy () {
		if ($this->lastBuy['done'] == 1 || 
			($this->lastBuy['done'] == 0 && $this->lastBuy['iid'] != $this->book_id) ) {
			$query = "INSERT INTO
						" . $this->table_name . "_books_buy
					SET
						uid = ?, box_id = ?, iid = ?, bid = ?, ilink = ?, square_id = ?";

			$stmt = $this->conn->prepare($query);

			// bind values
			$stmt->bindParam(1, $this->u);
			$stmt->bindParam(2, $this->id);
			$stmt->bindParam(3, $this->book_id);
			$stmt->bindParam(4, $this->book_bid);
			$stmt->bindParam(5, $this->book_link);
			$stmt->bindParam(6, $this->book_square_id);

			if ($stmt->execute()) {
				// decrease book number
				$this->decreaseBookNum();
				file_put_contents(MAIN_PATH.'/data/box/'.$this->id.'.json', $this->book_square_id);
				if ($this->addPost('buy')) return true;
				else return false;
			} else {
				return false;
			}
		} 
		return true;
	}
*/	
	function decreaseBookNum () {
		$this->book_num--;
		$query = "UPDATE
					" . $this->table_name . "_books
				SET
					num = ?
				WHERE id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->book_num);
		$stmt->bindParam(2, $this->book_id);
		if ($stmt->execute()) return true;
		else return false;
	}
	function increaseBookNum () {
		$this->book_num++;
		$query = "UPDATE
					" . $this->table_name . "_books
				SET
					num = ?
				WHERE id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->book_num);
		$stmt->bindParam(2, $this->book_id);
		if ($stmt->execute()) return true;
		else return false;
	}

}
