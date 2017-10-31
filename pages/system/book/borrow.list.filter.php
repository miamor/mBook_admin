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
$in_storage = isset($_POST['in_storage']) ? $_POST['in_storage'] : -1;
$status = isset($_POST['status']) ? $_POST['status'] : -1;
$uid = isset($_POST['uid']) ? $_POST['uid'] : null;
$bid = isset($_POST['bid']) ? $_POST['bid'] : null;
$authorAr = array();
if (isset($_POST['author'])) {
	foreach ($_POST['author'] as $oneAu)
		$authorAr[] = $oneAu;
}

$_List = $book->getBorrow($uid, $bid, $status, $start, $records);
$num = $book->countBorrow($uid, $bid, $status);
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
		$pageHTML .= $book->showPages(0, $records, $pages, $start);
	} else {
		if ($start <= 1*$records) {
			$pageHTML .= $book->showPages(0, $records, 1, $start);
		} else $pageHTML .= $book->showPages(0, $records, 0, $start);

		if ($start > ($pages-1)*$records) { // page last/last
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $book->showPages($pages-1, $records, $pages, $start);
		}
		else if ($start > ($pages-2)*$records) { // page (last-1)/last
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $book->showPages($currentPage-1, $records, $pages, $start);
		}
		else if ($start > ($pages-3)*$records) { // page (last-2)/last
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $book->showPages($currentPage-1, $records, $pages, $start);
		}
		else {
			if ($start >= 2*$records) {
				if ($start > 2*$records) $pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
				$pageHTML .= $book->showPages($currentPage-1, $records, $currentPage+1, $start);
			} else if ($start > 0) {
				$pageHTML .= $book->showPages(2, $records, 3, $start);
			}
			// last pages
			$pageHTML .= '<li class="paginate_button disabled"><a href="#">...</a></li>';
			$pageHTML .= $book->showPages($pages, $records, $pages, $start);
		}
	}

	$nextSt = $start + $records;
	if ($nextSt > $num) $pageHTML .= '<li class="paginate_button next disabled" id="book-list_next"><a href="#">Next</a></li>';
	else $pageHTML .= '<li class="paginate_button next" id="book-list_next"><a href="?start='.$nextSt.'&records='.$records.'">Next</a></li>';

	$pageHTML .= '</ul>';

	$ar[] = '<div class="one-book-row thead">
			<div class="col-lg-1 one-book-actions">
				Actions
			</div>
			<div class="col-lg-3 one-book-donated">
				User
			</div>
			<div class="col-lg-6 one-book-title">
				Book
			</div>
			<div class="col-lg-2 one-book-num center">
				Status
			</div>
			<div class="clearfix"></div>
		</div>';
foreach ($_List as $bK => $bO) {
	if ($bO['stt'] == 0) $bO['sttTxt'] = 'Waiting...';
	if ($bO['stt'] == 1) $bO['sttTxt'] = 'Keeping';
	if ($bO['stt'] == 2) $bO['sttTxt'] = 'Returned';
	$ar[] = '<div class="one-book-row">
		<div class="col-lg-1 one-book-actions center">
			<label class="checkbox left">
				<input type="checkbox" value="1" name="bAr"/>
			</label>
			<a class="one-book-edit" href="?type='.$type.'&mode=edit&id='.$bO['id'].'"><i class="fa fa-cog"></i></a>
			<a class="one-book-delete" href="?type='.$type.'&mode=delete&id='.$bO['id'].'&do=delete"><i class="fa fa-times"></i></a>
		</div>
		<div class="col-lg-3 one-book-donated">
			<a href="'.$bO['user']['link'].'">'.$bO['user']['name'].'</a>
		</div>
		<div class="col-lg-6 one-book-title">
			<a title="'.$bO['book']['title'].'" href="'.$bO['book']['link'].'">
				'.$bO['book']['title'].'
			</a>
		</div>
		<div class="col-lg-2 one-book-num center">
			<span class="borrow-status" data-stt="'.$bO['stt'].'"></span>
			'.$bO['sttTxt'].'
		</div>
		<div class="clearfix"></div>
	</div>';
}
}
echo $pageHTML.'<div class="clearfix"></div>';
echo '<div class="book-list">'.implode('', $ar).'</div>';
echo '<div class="clearfix"></div><div class="left results-show">Hiển thị từ '.($start+1).' đến '.($start+$records).' trong tổng số '.$num.' kết quả</div> '.$pageHTML.'<div class="clearfix"></div>';
 ?>
