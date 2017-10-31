<?php
class Post extends Config {

	// database connection and table name
//	private $conn;
	private $table_name = "posts";
	protected $bid;
	public $isFeed = false;

	public function __construct() {
		parent::__construct();
	}

	// create product
	function create () {
		//write query
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					content = ?, uid = ?, gid = ?";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(1, $this->content);
		$stmt->bindParam(2, $this->uid);
		$stmt->bindParam(3, $this->gid);

		/* read id of new post
		$q = "SELECT id FROM ".$this->table_name." WHERE uid = ? AND gid = ? ORDER BY created DESC, id DESC LIMIT 0,1";
		$st = $this->conn->prepare($q);
		$st->bindParam(1, $this->u);
		$st->bindParam(2, $this->gid);
		$st->execute();
		$newPost = $st->fetch(PDO::FETCH_ASSOC);
		$newPost['link'] = $this->sLink.'/'.$row['id'];*/

		if ($stmt->execute()) {
			// read id of new post
			$newPost = $this->sReadOne();
			$this->id = $newPost['id'];
			$this->link = $newPost['link'];
			return true;
		} else return false;

	}

	function sReadOne () {
		$query = "SELECT id FROM
					" . $this->table_name . "
				WHERE
					content = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->content);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['link'] = $this->sLink.'/'.$row['id'];
		return $row;
	}

	public function countAll(){
		$query = "SELECT id FROM " . $this->table_name . "";

		$stmt = $this->conn->prepare( $query );
		$stmt->execute();

		$num = $stmt->rowCount();

		return $num;
	}

	function readAll ($gid = null, $u = null, $page = 0) {
		$order = "modified DESC, created DESC, id DESC";

		if (!$gid) $gid = $this->gid;
		if ($gid) $cond = "WHERE gid = ?";

		$numPerPage = 5;
		$start = $page*$numPerPage;
		$lim = "LIMIT {$start}, {$numPerPage}";

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "
					{$cond}
				ORDER BY
					{$order}
				{$lim}";

		$stmt = $this->conn->prepare($query);
		if ($gid) $stmt->bindParam(1, $this->gid);

		$stmt->execute();

		$this->all_list = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['link'] = $this->sLink.'/'.$row['id'];
			$row['author'] = $this->getUserInfo($row['uid']);

			// content
			$row['content'] = content($row['content']);

			// likes
			$row['likesNum'] = 0;
			$row['likesAr'] = array();
			if ($row['likes']) {
				$likesAr = explode(',', $row['likes']);
				$uLikes = array();
				foreach ($likesAr as $oS)
					$uLikes[] = $this->getUserInfo($oS);
				$row['likesAr'] = $uLikes;
				$row['likesNum'] = count($likesAr);
			}
			// dislikes
			$row['dislikesNum'] = 0;
			$row['dislikesAr'] = array();
			if ($row['dislikes']) {
				$dislikesAr = explode(',', $row['dislikes']);
				$uDislikes = array();
				foreach ($dislikesAr as $oS)
					$uDislikes[] = $this->getUserInfo($oS);
				$row['dislikesAr'] = $uDislikes;
				$row['dislikesNum'] = count($dislikesAr);
			}

			// comments
//			$row['ratingsList'] = $this->getComments($row['id'], 2);
//			$row['ratingsNum'] = $this->countComments($row['id']);
			$this->getComments($row['id'], 2);
			$row['ratingsList'] = $this->ratingsList;
			$row['ratingsNum'] = $this->rTotal;

			// href
			$row['href'] = '';
			if (!$row['type']) {
				$row['type'] = 'status';
				$row['iid'] = $row['id'];
				if (!$row['href'])
					$row['href'] = 'status/'.$row['id'];
			} else if ($row['type'] == 'review') {
				$row['href'] = 'review/'.$row['id'];
			}

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
					id = ?
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);

		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->id = $row['id'];
		if ($row['id']) {
			$row['link'] = $this->sLink.'/'.$row['id'];
			$this->uid = $row['uid'];
			$this->content = $row['content'];
			// post to group
			if ($row['gid']) {
				$row['gIn'] = $this->getGroupInfo($row['gid']);
			}
			// author
			$row['author'] = $this->getUserInfo($row['uid']);
			// content
//			$row['content_feed'] = (strlen($row['content']) > 1000) ? content(substr(htmlspecialchars(strip_tags($row['content'])), 0, 1000)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="stt-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row['content_feed'] = (strlen($row['content']) > 1000) ? content(substr($row['content'], 0, 1000)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="stt-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row['content'] = content($row['content']);

			// share list
			$row['shareNum'] = $this->getShareNum();
/*			$row['share'] = array();
			if ($row['share']) {
				$shareAr = explode(',', $row['share']);
				foreach ($shareAr as $oS)
					$uShare[] = $this->getUserInfo($oS);
				$row['share'] = $uShare;
				$row['shareNum'] = count($shareAr);
			}
*/
			// likes
			$row['likesNum'] = 0;
			$row['likesAr'] = $likesUAr = $uLikes = array();
			$this->likes = $row['likes'];
			$row['likeShow'] = '<a href="#" id="'.$this->id.'" class="show-likes-list"> thích bài viết này</a>';
			if ($row['likes']) {
				$likesAr = explode(',', $row['likes']);
				$uLikes = array();
				foreach ($likesAr as $oS) {
					$oS = intval(preg_replace('/[^0-9]+/', '', $oS), 10);
					$likesUAr[] = $oS;
					$uLikes[] = $this->sGetUserInfo($oS);
				}
				$row['likesAr'] = $uLikes;
				$this->likesAr = $likesUAr;
				$row['likesNum'] = $likesNum = count($likesAr);
				if (in_array($this->u, $this->likesAr)) {
					if ($likesNum > 1) {
						$ls = '<span class="your-like">Bạn và </span>'.($likesNum-1).' người khác';
					} else {
						$ls = '<span class="your-like">Bạn</span>';
					}
				} else {
					$ls = $uLikes[0]['name'];
					if ($likesNum > 1) {
						$ls .= ' và '.($likesNum-1).' người khác';
					}
				}
				$row['likeShow'] = '<a href="#" id="'.$this->id.'" class="show-likes-list">'.$ls.' thích bài viết này</a>';
			}
			$this->myLike = in_array($this->u, $this->likesAr);
			// dislikes
/*			$row['dislikesNum'] = 0;
			$row['dislikesAr'] = array();
			if ($row['dislikes']) {
				$dislikesAr = explode(',', $row['dislikes']);
				$uDislikes = array();
				foreach ($dislikesAr as $oS)
					$uDislikes[] = $this->sGetUserInfo($oS);
				$row['dislikesAr'] = $uDislikes;
				$row['dislikesNum'] = count($dislikesAr);
			}
*/
			// comments
//			$row['ratingsList'] = $this->getComments();
//			$row['ratingsNum'] = $this->countComments();
			$this->getComments();
			$row['ratingsList'] = $this->ratingsList;
			$row['ratingsNum'] = $this->rTotal;
		}
		return $row;
	}

	function getShareNum () {
		$query = "SELECT id FROM ".$this->table_name."_share WHERE iid = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->execute();
		return $stmt->rowCount();
	}
	function checkMyShareFB () {
		$query = "SELECT id FROM ".$this->table_name."_share WHERE iid = ? AND uid = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $this->id);
		$stmt->bindParam(2, $this->u);
		$stmt->execute();
		return $stmt->rowCount();
	}
	function shareFB () {
		if ($this->checkMyShareFB() < 1) {
			$query = "INSERT INTO ".$this->table_name."_share SET uid = ?, iid = ?, fb_post_id = ?";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(1, $this->u);
			$stmt->bindParam(2, $this->id);
			$stmt->bindParam(3, $this->fb_post_id);
			if ($stmt->execute()) {
/*				// add coin for user who writes this
				$this->addCoin(COINS_SHARE_USER_WRITE_STATUS, $this->uid);
				// add coin for user who shares ($this->u)
				$this->addCoin(COINS_SHARE_STATUS);
*/
				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'share-status',
						'iid' => $this->u,
						'post_id' => $this->id,
						'content' => json_encode(array(
									'post_id' => $this->id,
									'fb_post_id' => $this->fb_post_id
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);

				return true;
			}
			else return false;
		} else return false;
	}

	function like () {
		$this->likesNum = count($this->likesAr);
		if (!in_array($this->u, $this->likesAr)) {
			$this->likes = str_replace(' ', ',', trim($this->likes." [{$this->u}]"));
			$this->likesNum++;
		} else {
			$this->likes = str_replace("[{$this->u}]", '', $this->likes);
			$this->likes = str_replace(',', ' ', $this->likes);
			$this->likes = str_replace(' ', ',', trim($this->likes));
			$this->likesNum--;
		}
		$query = "UPDATE
					" . $this->table_name . "
				SET
					likes  = :likes
				WHERE
					id = :id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':likes', $this->likes);
		$stmt->bindParam(':id', $this->id);
		if ($stmt->execute()) {
				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'like-post',
						'iid' => $this->u,
						'post_id' => $this->id,
						'content' => json_encode(array(
									'post_id' => $this->id,
									'post_total_likes' => $this->likesNum,
									'content' => htmlspecialchars(strip_tags($this->content)),
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);

			return true;
		}
		else return false;
	}

	function getGroupInfo ($gid) {
		$query = "SELECT
					title,code,status
				FROM
					groups
				WHERE
					id = ?
				LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $gid);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() > 0) $row['link'] = $this->grLink.'/'.$row['code'];

		return $row;
	}

	function sReadCmtOne () {
		$query = "SELECT id FROM
					" . $this->table_name . "_comments
				WHERE
					content = ? AND iid = ?
				LIMIT 0,1";
		$stmt = $this->conn->prepare($query);
//		$this->rContent = content($this->rContent);
		$stmt->bindParam(1, $this->rContent);
		$stmt->bindParam(2, $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	function reply () {
		$query = "INSERT INTO
					" . $this->table_name . "_comments
				SET
					content = ?, iid = ?, uid = ?";

		$stmt = $this->conn->prepare($query);

		// posted values
//		$this->rContent = content($this->content);

		// bind values
		$stmt->bindParam(1, $this->rContent);
		$stmt->bindParam(2, $this->id);
		$stmt->bindParam(3, $this->u);

		if ($stmt->execute()) {
				// add noti for user who writes this
				$valAr = array(
						'from_uid' => $this->u,
						'type' => 'comment-post',
						'post_id' => $this->id,
						'content' => json_encode(array(
									'content' => htmlspecialchars(strip_tags($this->rContent)),
									'post_id' => $this->id
								), JSON_UNESCAPED_UNICODE)
					);
				$this->addNoti($valAr, $this->uid);

			return true;
		}
		else return false;
	}

/*	protected function getComments ($id = null, $order = null) {
		if (!$order) $order = "modified DESC, created DESC, id DESC";
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_comments
				WHERE
					iid = ?
				ORDER BY
					{$order}";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(1, $id);

		$stmt->execute();

		$totalReview = 0;
		$totalRates = 0;
		$this->ratingsList = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['author'] = $this->getUserInfo($row['uid']);

//			$row['content'] = content(substr($row['content'], 0, 1200)).'... <a href="'.$row['link'].'" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>';
			$cont = htmlspecialchars(strip_tags($row['content']));
			$row['short_content'] = (strlen($cont) > 280) ? content(substr($cont, 0, 280)).'... <a href="#" class="see-more" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];
			$row['content_feed'] = (strlen($cont) > 1500) ? content(substr($cont, 0, 1500)).'... <a href="#" class="see-more" id="'.$row['id'].'" class="book-rv-read gensmall">Xem đầy đủ</a>' : $row['content'];

			$this->ratingsList[] = $row;
		}

		if ($totalReview == 0) $averageRate = 0;
		else $averageRate = $totalRates/$totalReview;
		if (($averageRate - floor($averageRate)) >= 0.5) $averageRate = floor($averageRate) + 0.5;
		else $averageRate = floor($averageRate);

		$this->averageRate = number_format($averageRate, 1);
		$this->totalReview = $totalReview;

		return $stmt;
	}
*/
	function getComments ($id = '', $order = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_comments
				WHERE
					iid = ?";

		$valAr = array($id);
		$this->ratingsList = $this->_getRatings($query, $valAr, false);

		return $this->ratingsList;
	}

/*	function countComments ($id = '') {
		if (!$id) $id = $this->id;

		$query = "SELECT
					*
				FROM
					" . $this->table_name . "_comments
				WHERE
					iid = ?";

		$valAr = array($id);
		$num = $this->_countRatings($query, $valAr);

		return $num;
	}
*/

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
