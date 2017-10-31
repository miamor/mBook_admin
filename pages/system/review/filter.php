<?php
//print_r($_List);

$genresAr = array();
if (isset($_POST['genres'])) {
	foreach ($_POST['genres'] as $oneGen)
		$genresAr[] = $oneGen;
//	$genres = $_POST['genres'];
}

$start = isset($_POST['start']) ? $_POST['start'] : 0;
$records = isset($_POST['records']) ? $_POST['records'] : 24;
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
$bid = isset($_POST['bid']) ? $_POST['bid'] : null;
$avai = isset($_POST['avai']) ? $_POST['avai'] : 0;

$stmt = $review->getAll($uid, $bid, $avai, $start, $records);
$_List = $review->all_list;
$num = $review->countAll($uid, $bid, $avai);
//$num = count($_List);

$pageHTML = '';

if ($num <= 0) {
	$ar[] = '<div class="empty">Không có dữ liệu</div>';
} else {
	$pages = ceil($num/$records)-1;
	$pageHTML = '<ul class="pagination right">';

	$prevSt = $start - $records;
	if ($prevSt <= 0) $pageHTML .= '<li class="paginate_button previous disabled" id="book-list_previous"><a href="#">Previous</a></li>';
	else $pageHTML .= '<li class="paginate_button previous" id="book-list_previous"><a href="?start='.$prevSt.'&records='.$records.'">Previous</a></li>';

	$currentPage = $start/$records;

	if ($pages <= 3) {
		$pageHTML .= $review->showPages(0, $records, $pages, $start);
	} else {
		if ($start <= 1*$records) {
			$pageHTML .= $review->showPages(0, $records, 1, $start);
		} else $pageHTML .= $review->showPages(0, $records, 0, $start);

		if ($start > ($pages-1)*$records) { // page last/last
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $review->showPages($pages-1, $records, $pages, $start);
		}
		else if ($start > ($pages-2)*$records) { // page (last-1)/last
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $review->showPages($currentPage-1, $records, $pages, $start);
		}
		else if ($start > ($pages-3)*$records) { // page (last-2)/last
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $review->showPages($currentPage-1, $records, $pages, $start);
		}
		else {
			if ($start >= 2*$records) {
				if ($start > 2*$records) $pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
				$pageHTML .= $review->showPages($currentPage-1, $records, $currentPage+1, $start);
			} else if ($start > 0) {
				$pageHTML .= $review->showPages(2, $records, 3, $start);
			}
			// last pages
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $review->showPages($pages, $records, $pages, $start);
		}
	}

	$nextSt = $start + $records;
	if ($nextSt > $num) $pageHTML .= '<li class="paginate_button next disabled" id="book-list_next"><a href="#">Next</a></li>';
	else $pageHTML .= '<li class="paginate_button next" id="book-list_next"><a href="?start='.$nextSt.'&records='.$records.'">Next</a></li>';

	$pageHTML .= '</ul>';

	$ar[] = '<div class="one-book-row thead">
			<div class="col-lg-1 one-book-actions no-padding">
				Actions
			</div>
			<div class="col-lg-2 one-book-author">
				User
			</div>
			<div class="col-lg-2 one-book-title">
				Title
			</div>
			<div class="col-lg-4 one-book-preview">
				Preview
			</div>
			<div class="col-lg-2 one-book-stat">
				Ratings
			</div>
			<div class="clearfix"></div>
		</div>';
foreach ($_List as $bK => $bO) {
	$isUID = ($bO['uid'] > 0 && $bO['type'] == 0) ? 1 : 0;
	$ratings = $stick = '';
	for ($i = 1; $i <= 5; $i++) {
		if ($bO['average'] > $i && $bO['average'] < ($i+1)) $ratings .= '<i class="fa fa-star-half-o"></i>';
		else if ($bO['average'] < $i) $ratings .= '<i class="fa fa-star-o"></i>';
		else $ratings .= '<i class="fa fa-star"></i>';
	};

	$createLink = '';
	if ($bO['book']['id']) $bO['title'] = '<a title="'.$bO['book']['title'].'" href="'.$bO['book']['link'].'">'.$bO['book']['title'].'</a>';
	else $createLink = '<a href="'.$config->bLink.'?mode=new">Add</a>';

	$stat = '<div class="feed-ratings stat-one">
			<strong class="text-warning left">'.$bO['average'].'</strong>
			<span class="ratings text-warning left">
				'.$ratings.'
			</span>
			<div class="clearfix"></div>
			<a class="gensmall" href="'.PAGE_URL.'/review/'.$bO['id'].'">('.$bO['ratingsNum'].' reviews)</a>
		</div>';

	$ar[] = '<div class="one-book-row" data-id="'.$bO['id'].'">
		<div class="col-lg-1 no-padding one-book-actions">
			<label class="checkbox left" style="margin:1px 5px 0 0">
				<input type="checkbox" value="1" name="bAr"/>
			</label>
			<a class="one-book-edit" href="'.$bO['link'].'"><i class="fa fa-cog"></i></a>
			<span class="one-book-status" data-stt="'.$bO['show'].'"></span>
		</div>
		<div class="col-lg-2 one-book-author">
			<a href="'.$bO['author']['link'].'">'.$bO['author']['name'].'</a>
		</div>
		<div class="col-lg-2 one-book-title">
			'.$bO['title'].' '.$createLink.'
		</div>
		<div class="col-lg-4 one-book-preview">
			'.substr(htmlentities($bO['content']), 0, 100).'
		</div>
		<div class="col-lg-2 one-book-stat">
			'.$stat.'
		</div>
		<div class="clearfix"></div>
	</div>';
}
}
echo $pageHTML.'<div class="clearfix"></div>';
echo '<div class="book-list">'.implode('', $ar).'</div>';
echo '<div class="clearfix"></div><div class="left results-show">Hiển thị từ '.($start+1).' đến '.($start+$records).' trong tổng số '.$num.' kết quả</div> '.$pageHTML.'<div class="clearfix"></div>';
 ?>
