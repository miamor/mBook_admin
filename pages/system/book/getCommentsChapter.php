<?php
foreach ($bChap['ratingsList'] as $rK => $rO) {
	$ratingsIcon = '';
	for ($i = 1; $i <= 5; $i++) {
		if ($rO['rate'] > $i && $rO['rate'] < ($i+1)) $ratingsIcon .= '<i class="fa fa-star-half-o"></i>';
		else if ($rO['rate'] < $i) $ratingsIcon .= '<i class="fa fa-star-o"></i>';
		else $ratingsIcon .= '<i class="fa fa-star"></i>';
	}
	$ar['data'][] = array(
		$rO['created'],
		$rK,
		'<div class="box review-one-comment">
	<div class="box-header with-border">
		<div class="box-comment-left">
			<a href="'.$rO['author']['link'].'" data-online="'.$rO['author']['online'].'" class="left">
				<img class="img-sm img-circle" src="'.$rO['author']['avatar'].'">
			</a>
		</div>
		<div class="box-username">
			<a href="'.$rO['author']['link'].'">
				'.$rO['author']['name'].'
			</a>
			<span class="ratings text-warning">
			'.$ratingsIcon.'
			</span>
			<span class="coins-plus" title="Review của '.$rO['author']['name'].' đã cộng thêm cho '.$bChap['author']['name'].' '.$rO['coins'].' điểm">
				<span class="text-success">+'.$rO['coins'].'</span>
			</span>
			<span class="text-time text-muted pull-right">'.$rO['created'].'</span>
		</div><!-- .username -->
	</div>
	<div class="box-body">
		<div class="comment-text">
			<h4 class="rating-title">'.$rO['title'].'</h4>
			<div class="rating-content">
				'.$rO['content'].'
			</div>
		</div><!-- .comment-text -->
	</div>
</div><!-- .review-one-comment -->'
	);
}
echo json_encode($ar);
 ?>