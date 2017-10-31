<div class="feed-one-item feed-item-review">
	<div class="feed-content feed-rv">
		<div class="box box-review feed-main col-lg-8 feed-rv-main">
			<div class="box-header feed-main-head feed-rv-head">
				<div class="feed-ratings right ratings text-lg text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($rate > $i && $rate < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($rate < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
				</div>
				<?php echo '<a href="'.$link.'" title="Xem đầy đủ">#review</a>' ?>
				<i class="fa fa-caret-right to-caret"></i>
				<?php echo ($iid) ? '<a href="'.$book['link'].'">'. $book['title'] .'</a>' : $title ?>
			</div>
			<div class="box-body feed-main-content feed-rv-content">
				<?php echo $content_feed ?>
				<div class="feed-rv-thumb">
					<img class="feed-rv-thumb-img" src="<?php echo $thumb ?>"/>
					<?php if ($review->toFB_html) echo '<div class="link-to-fb-post">'.$review->toFB_html.'</div>' ?>
				</div>
			</div>

			<div class="box-footer stat feed-sta">
				<div class="feed-ratings stat-one col-lg-6 no-padding">
					<strong class="text-warning"><?php echo $review->rAverage ?></strong>
					<span class="ratings text-warning">
						<?php for ($i = 1; $i <= 5; $i++) {
							if ($review->rAverage > $i && $review->rAverage < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
							else if ($review->rAverage < $i) echo '<i class="fa fa-star-o"></i>';
							else echo '<i class="fa fa-star"></i>';
						} ?>
					</span>
					<span class="gensmall">(<?php echo $review->rTotal ?> ratings)</span>
				</div>
				<div class="feed-coins text-success stat-one col-lg-3 no-padding text-center">
					+<strong><?php echo $review->rCoins ?></strong>
					coins
				</div>
				<div class="feed-share text-info stat-one col-lg-3 no-padding text-right">
<!--					<a class="share" data-param="link=http://jspatterns.com&amp;app_id=<?php echo FB_APP_ID ?>&amp;redirect_uri=<?php echo $review->link.'?do=shareFB' ?>"><strong id="share_num_review_<?php echo $review->id ?>"><?php echo $shareNum ?></strong> Share</a>
-->
				<?php if ($review->checkMyShareFB()) echo '<a class="shared"><i class="fa fa-check"></i> <strong id="share_num_review_'. $review->id .'">'.$shareNum .'</strong> Share</a>';
				else echo '<a class="share" data-param="link='. $review->link.'&amp;app_id='. FB_APP_ID .'&amp;redirect_uri='. $review->link.'?do=shareFB"><strong id="share_num_review_'. $review->id .'">'. $shareNum .'</strong> Share</a>'; ?>
				</div>
			</div>

			<div class="box-footer box-comments">
<?php 	$parentPageView = $rView;
		include 'pages/views/_temp/cmtList.feed.php';
		$fType = 'review'; $fID = $review->id;
		include 'pages/views/_temp/cmtForm.feed.php' ?>
			</div><!-- /.box-footer -->

		</div>

		<div class="col-lg-4 no-padding-right feed-rv-book">
<?php if (!$iid) {
	echo '<div class="not-available alerts alert-warning no-margin">Cuốn sách này chưa có sẵn trong thư viện eBook của mBook. <a href="'.$config->bLink.'?mode=new">Thêm</a></div>';
} else { ?>
			<a href="<?php echo $book['link'] ?>" title="<?php echo $book['title'] ?>">
				<img class="book-thumb" src="<?php echo $book['thumb'] ?>">
			</a>
			<div class="book-rate">
				<div class="book-score left text-warning">
					<?php echo $book['averageRate'] ?>
				</div>
				<div class="book-ratings-details">
					<div class="ratings book-ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($book['averageRate'] > $i && $book['averageRate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($book['averageRate'] < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
					</div>
					<a href="<?php echo $book['link'].'/reviews' ?>" title="View all <?php echo $book['totalReview'] ?> reviews" class="gensmall">(<?php echo $book['totalReview'] ?> reviews)</a>
				</div>
			</div>
			<div class="book-details no-padding">
				<div class="">
					<div class="book-genres">
						<b>Thể loại:</b> <?php echo $book['genresText'] ?>
					</div>
					<div class="book-authors">
						<b>Tác giả:</b> <a href="<?php echo $book['author']['link'] ?>"><?php echo $book['author']['name'] ?></a>
					</div>
				</div>
			</div>
<?php } ?>
		</div>

		<div class="clearfix"></div>
	</div>

	<div class="clearfix"></div>
</div>
