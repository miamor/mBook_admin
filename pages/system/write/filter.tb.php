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
$authorAr = array();
if (isset($_POST['author'])) {
	foreach ($_POST['author'] as $oneAu)
		$authorAr[] = $oneAu;
}

$stmt = $book->readAll('', $genresAr, $authorAr, '', '', '', $keyword, $in_storage);
$_List = $book->all_list;
//$num = $book->countAll('', $genresAr, $authorAr, $keyword);
//$num = count($_List);

$pageHTML = '';

if (count($_List) <= 0) {
	$ar['data'] = array();
} else {

foreach ($_List as $bK => $bO) {
	$isUID = ($bO['uid'] > 0 && $bO['type'] == 0) ? 1 : 0;
	$ratings = $stick = '';
	for ($i = 1; $i <= 5; $i++) {
		if ($bO['averageRate'] > $i && $bO['averageRate'] < ($i+1)) $ratings .= '<i class="fa fa-star-half-o"></i>';
		else if ($bO['averageRate'] < $i) $ratings .= '<i class="fa fa-star-o"></i>';
		else $ratings .= '<i class="fa fa-star"></i>';
	};
	if ($bO['published'] == 1) $stick .= '<div title="Tác phẩm này đã được xuất bản" class="one-book-published"></div>';
	if ($bO['type'] == 0 && $bO['uid']) $stick .= '<div title="Tác phẩm này được viết bởi thành viên của mBook" class="one-book-written"></div>';
	if ($bO['type'] == 1) $stick .= '<div title="Đây là một chủ đề" class="one-book-topic"></div>';

	//print_r($bO);
	if ($bO['in_storage'] == 1) $stick .= '<div title="Có sẵn trong kho sách. Số lượng: '.$bO['num_in_storage'].'" class="one-book-in_storage">'.$bO['num_in_storage'].'</div>';

	if ($bO['type'] == 0) {
		$chapNumTxt = '<div class="one-book-chapters center">
						<span>'.$bO['chaptersNum'].'</span> chương
					</div>';
		$stat = '<div class="feed-ratings stat-one">
				<strong class="text-warning left">'.$bO['averageRate'].'</strong>
				<span class="ratings text-warning left">
					'.$ratings.'
				</span>
				<a class="gensmall" href="'.$bO['link'].'/reviews'.'">('.$bO['totalReview'].' reviews)</a>
			</div>';
	} else { // is topic
		$chapNumTxt = '';
		$stat = '<div class="feed-ratings topic-replies">
				<strong class="text-success">'.$bO['chaptersNum'].'</strong>
				bài viết
			</div>';
	}

	$ar['data'][] = array(
		'stt' => '',
		'id' => $bO['id'],
		'title' => $bO['title'],
		'author' => '<a href="'.$bO['author']['link'].'">'.$bO['author']['name'].'</a>',
		'genres' => $bO['genresText'],
		'ratings' => $stat,
		'added_uid' => '<a href="'.$bO['author']['link'].'">'.$bO['author']['name'].'</a>',
		'action' => ''
	);
}
}
echo json_encode($ar, JSON_UNESCAPED_UNICODE);
 ?>
