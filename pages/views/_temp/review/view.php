<div class="col-lg-1"></div>

<div class="review-info col-lg-10 no-padding feed-one-item feed-item-review" data-iid="<?php echo $review->id ?>">
	<div class="col-lg-9 no-padding-left">
		<div class="feed-user-info col-lg-1 no-padding centered">
			<a href="<?php echo $author['link'] ?>" data-online="<?php echo $author['online'] ?>">
				<img class="feed-user-avt img-circle" src="<?php echo $author['avatar'] ?>">
			</a>
		</div>
		<div class="feed-content col-lg-11 no-padding-right feed-rv">
			<div class="feed-main feed-rv-main box box-review">
				<div class="box-header feed-main-head feed-rv-head">
					<div class="feed-ratings right ratings text-lg text-warning">
						<?php for ($i = 1; $i <= 5; $i++) {
							if ($rate > $i && $rate < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
							else if ($rate < $i) echo '<i class="fa fa-star-o"></i>';
							else echo '<i class="fa fa-star"></i>';
						} ?>
					</div>
					<a href="<?php echo $author['link'] ?>"><?php echo $author['name'] ?></a> đã thêm 1 <a href="<?php echo $link ?>">review</a> cho cuốn sách <a href="<?php echo $book['link'] ?>"><?php echo $book['title'] ?></a>
				</div>
				<div class="box-body feed-main-content feed-rv-content">
					<div class="feed-rv-thumb">
						<img class="feed-rv-thumb-img" src="<?php echo $thumb ?>"/>
						<?php if ($review->toFB_html) echo '<div class="link-to-fb-post">'.$review->toFB_html.'</div>' ?>
					</div>
					<?php echo $content ?>
				</div>

				<div class="box-footer stat feed-sta">
					<div class="feed-ratings stat-one col-lg-5 no-padding">
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
					<div class="feed-share text-info stat-one col-lg-4 no-padding text-right">
					<?php if ($review->checkMyShareFB()) echo '<a class="shared"><i class="fa fa-check"></i> <strong id="share_num_review_'. $review->id .'">'.$shareNum .'</strong> Share</a>';
					else echo '<a class="share" data-param="link='. $review->link.'&amp;app_id='. FB_APP_ID .'&amp;redirect_uri='. $review->link.'?do=shareFB"><strong id="share_num_review_'. $review->id .'">'. $shareNum .'</strong> Share</a>'; ?>
					</div>
				</div>
			</div> <!-- .box-review -->

		</div>

		<div class="clearfix"></div>

		<div class="r-cmts" id="comments">
		<h3>Bình luận</h3>
		<table class="etable review-comments" id="r_comments">
			<thead class="hidden">
				<tr>
					<th class="th-none hidden"></th>
					<th class="th-none hidden"></th>
					<th class="th-none hidden"></th>
				</tr>
			</thead>
		</table> <!-- /.review-comments #r_comments -->
		</div>

		<?php include 'pages/views/_temp/cmtForm.php' ?>
	</div>

	<div class="col-lg-3 feed-book-info no-padding feed-rv-book">
<?php if (!$iid) {
	echo '<div class="not-available alerts alert-warning no-margin">Cuốn sách này chưa có sẵn trong thư viện eBook của mBook.</div>';
} else { ?>
		<a data-uid="<?php echo ($book['uid']) ? 1 : 0 ?>" data-published="<?php echo ($book['published']) ? 1 : 0 ?>" href="<?php echo $book['link'] ?>" title="<?php echo $book['title'] ?>">
			<img class="book-thumb" src="<?php echo $book['thumb'] ?>">
			<?php if ($book['uid']) echo '<div title="Tác phẩm này được viết bởi thành viên của mBook" class="one-book-written"></div>';
			if ($book['published']) echo '<div title="Tác phẩm này đã được xuất bản" class="one-book-published"></div>'; ?>
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
			<div class="book-genres">
				<b>Thể loại:</b> <?php echo $book['genresText'] ?>
			</div>
			<div class="book-status">
				<b>Tình trạng:</b> <?php echo $book['sttText'] ?>
			</div>
			<div class="book-authors">
				<b>Tác giả:</b> <a href="<?php echo $book['author']['link'] ?>"><?php echo $book['author']['name'] ?></a>
			</div>
		</div>
		<?php
		if ($book['in_storage']) {
			echo '<a href="'.$book['link'].'#in_storage" class="btn btn-danger btn-block book-in_storage">Có sẵn trong kho sách ('.$book['num_in_storage'].')</a>';
		}
		?>
<?php } ?>
	</div>

	<div class="clearfix"></div>
</div>

<div class="col-lg-1"></div>

<div class="clearfix"></div>
