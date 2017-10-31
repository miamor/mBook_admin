<?php
class Prints extends Config {

	private $pHash;
	private $table_name = "prints";

	public function __construct() {
		parent::__construct();
		require_once(MAIN_PATH.'/include/phash.class.php');
		
		$this->pHash = pHash::Instance();
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
		$this->search();
		// get print info in database
		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
				WHERE
					link = ?
				LIMIT
					0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->bookLink);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		
		if ($row['id']) {
			$row['bIn'] = $this->getBookInfo($row['iid']);
			$row['pIn'] = $this->getUserInfo($row['publisher']);
		}
		return $row;
	}

	function resize ($file, $w = 800, $h = 800, $crop = false) {
/*		$ext = end(explode('.', $file));
		$name = explode('.'.$ext, $file)[0].'_resize';
		$outputFile = $name.'.'.$ext;
		$image = imagecreatefromjpeg($outputFile);
		//700 for the width you want... imagesx() to determine the current width
		$ratio = 700 / imagesx($image); 
		// imagesy() to determine the current height
		$height = imagesy($image) * $ratio; 
		// resize 
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
		$image = $new_image; // $image has now been replaced with the resized one.
*/
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
		echo '{'.$width.' - '.$height.' - '.$r.' @ '.$newwidth.' - '.$newheight.'}';
		$src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
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
		$ext = end(explode('.', $file['name']));
		$name = explode('.'.$ext, $file['name'])[0];
		if (!$isFront) $name .= '_back';
		$fname = $name.'.'.$ext;
		$new_path = MAIN_PATH.'/data/img/books/'.$fname;
		if ($file['error'] > 0) {
			return false;
		} else {
			move_uploaded_file($file['tmp_name'], $new_path);
		}
		return $new_path;
	}

	function addByCover () {
		$cover = $this->coverIMG;
		$title = $this->title;
		$code = encodeURL($title);
		$barcode = $this->barcode;
		/* resize */
		$img = $this->resize($this->coverIMG);

		/* get pHash */
		//$hash = $this->HashImage($img);
		$pixels = $this->pHash->getPixImage($img);
		$pixStr = implode(',', $pixels);

		// add cover data to books.txt
		$txt = $pixStr.','.$code;
		$file = MAIN_PATH."/data/books.txt";
		$contents = file_get_contents($file);
		$pattern = "/^.*{$txt}.*\$/m";
		// search, and store all matching occurences in $matches
		if (!preg_match_all($pattern, $contents, $matches)) {
			$addData = file_put_contents($file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
			if ($addData) $stt = 1;
			else $stt = -1;
		} else $stt = -2; //'Data already existed.';

		$ar = array(
			'title' => $title,
			'code' => $code,
			'stt' => $stt,
			'data' => $txt,
		);
		return $ar;
	}

	function addByBarcode () {
		$title = $this->title;
		$code = encodeURL($title);
		/* resize */
		$img = $this->resize($this->backIMG);
		// barcode is EN13
		// 8 936024 917401
		// 1-6: factory code
		$factoryCode = substr($this->barcode, 1, 6);
		$itemCode = substr($this->barcode, 7, 12);
		// add barcode data to factoryCode.txt
		$txt = '['.$itemCode.']{'.$code.'}<'.$this->edition.'>';
		// add to books.txt
		$file = MAIN_PATH."/data/{$factoryCode}.txt";
		$contents = file_get_contents($file);
		$pattern = "/^.*{$txt}.*\$/m";
		// search, and store all matching occurences in $matches
		if (!preg_match_all($pattern, $contents, $matches)) {
			$addData = file_put_contents($file, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
			if ($addData) $stt = 1;
			else $stt = -1;
		} else $stt = -2; //'Data already existed.';

		$ar = array(
			'title' => $title,
			'code' => $code,
			'stt' => $stt,
			'data' => $txt,
		);
		return $ar;
	}
	
	function search () {
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

}