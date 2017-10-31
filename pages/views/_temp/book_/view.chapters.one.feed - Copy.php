<div class="feed-one-item feed-item-chapter">
	<div class="feed-user-info col-lg-1 no-padding centered">
		<a href="<?php echo $bChap['author']['link'] ?>" data-online="<?php echo $bChap['author']['online'] ?>">
			<img class="feed-user-avt img-circle" src="<?php echo $bChap['author']['avatar'] ?>">
		</a>
	</div>
	<div class="feed-content col-lg-11 no-padding feed-wr">
		<div class="box box-chapter feed-main col-lg-8 feed-wr-main">
			<div class="box-header feed-main-head feed-wr-head">
				<a href="<?php echo $bChap['author']['link'] ?>"><?php echo $bChap['author']['name'] ?></a> đã thêm 1 <a href="<?php echo $bChap['title'] ?>">chương</a> mới vào <a href="<?php echo $bChap['link'] ?>"><?php echo $bChap['title'] ?></a>
			</div>
			<div class="box-body feed-main-content feed-wr-content">
				<?php echo $bChap['content_feed'] ?>
			</div>

			<div class="box-footer stat feed-sta">
				<div class="feed-ratings stat-one col-lg-6 no-padding">
					<strong class="text-warning"><?php echo $chapter->averageRate ?></strong>
					<span class="ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($chapter->averageRate > $i && $chapter->averageRate < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($chapter->averageRate < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
					</span>
					<span class="gensmall">(<?php echo $chapter->totalReview ?> reviews)</span>
				</div>
				<div class="feed-coins text-success stat-one col-lg-6 no-padding text-right">
					<strong title="Chương này đã cộng thêm cho <?php echo $bChap['author']['name'] ?> <?php echo $chapter->coins ?> điểm">+<?php echo $chapter->coins ?></strong>
					coins
				</div>
			</div>
			
	<?php if ($bChap['ratingsNum'] > 0) { ?>
			<div class="box-footer box-comments">
	<?php 	if ($bChap['ratingsNum'] > 2) echo '<div class="box-comment"><div class="comment-text"><a href="'.$bChap['link'].'"><i class="fa fa-refresh"></i> Xem tất cả '.$ratingsNum.' bình luận</a></div></div>';
			$rO = $bChap['ratingsList'][0] ?>
				<div class="box-comment">
					<div class="box-comment-left">
						<a href="<?php echo $rO['author']['link'] ?>" data-online="<?php echo $rO['author']['online'] ?>" class="left">
							<img class="img-sm img-circle" src="<?php echo $rO['author']['avatar'] ?>">
						</a>
					</div>
					<div class="comment-text">
						<span class="username">
							<a href="<?php echo $rO['author']['link'] ?>"><?php echo $rO['author']['name'] ?></a>
							<span class="ratings text-warning">
							<?php for ($i = 1; $i <= 5; $i++) {
								if ($rO['rate'] > $i && $rO['rate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
								else if ($rO['rate'] < $i) echo '<i class="fa fa-star-o"></i>';
								else echo '<i class="fa fa-star"></i>';
							} ?>
							</span>
							<span class="coins-plus" title="Review của <?php echo $rO['author']['name'] ?> đã cộng thêm cho <?php echo $bChap['author']['name'] ?> <?php echo $rO['coins'] ?> điểm">
								<span class="text-success">+<?php echo $rO['coins'] ?></span>
							</span>
							<span class="text-muted pull-right"><?php echo $rO['created'] ?></span>
						</span><!-- /.username -->
						<?php echo $rO['content'] ?>
					</div><!-- /.comment-text -->
				</div><!-- /.box-comment -->
			</div><!-- /.box-footer -->
	<?php } ?>
		</div>

		<div class="col-lg-4 no-padding-right feed-wr-book">
			<a title="<?php echo $title ?>" href="<?php echo $link ?>" title="<?php echo $title ?>">
				<img class="book-thumb" src="<?php echo $thumb ?>">
			</a>
			<div class="book-rate">
				<div class="book-score left text-warning">
					<?php echo $book->averageRate ?>
				</div>
				<div class="book-ratings-details">
					<div class="ratings book-ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($book->averageRate > $i && $book->averageRate < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($book->averageRate < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
					</div>
					<a href="<?php echo $link.'/reviews' ?>" title="View all <?php echo $book->totalReview ?> reviews" class="gensmall">(<?php echo $book->totalReview ?> reviews)</a>
				</div>
			</div>
			<div class="book-details no-padding">
				<div class="book-genres">
					<b>Thể loại:</b> <?php echo $genresText ?>
				</div>
				<div class="book-authors">
					<b>Tác giả:</b> <a href="<?php echo $author['link'] ?>"><?php echo $author['name'] ?></a>
				</div>
				<div class="book-status">
					<b>Tình trạng:</b> <?php echo $sttText ?>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>
	
	<div class="clearfix"></div>
</div>


