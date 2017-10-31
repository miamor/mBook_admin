<div class="feed-one-item feed-item-chapter">
	<div class="feed-content feed-wr">
		<div class="box box-chapter feed-main col-lg-8 feed-wr-main">
			<div class="box-header feed-main-head feed-wr-head">
				<a href="<?php echo $bChap['link'] ?>"><?php echo $bChap['title'] ?></a>
				<i class="fa fa-caret-right to-caret"></i>
				<a href="<?php echo $book->link ?>"><?php echo $book->title ?></a>
			</div>
			<div class="box-body feed-main-content feed-wr-content">
				<?php echo $bChap['content_feed'] ?>
			</div>

			<div class="box-footer stat feed-sta">
				<div class="feed-ratings stat-one col-lg-6 no-padding">
					<strong class="text-warning"><?php echo $chapter->rAverage ?></strong>
					<span class="ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($chapter->rAverage > $i && $chapter->rAverage < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($chapter->rAverage < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
					</span>
					<span class="gensmall">(<?php echo $chapter->rTotal ?> reviews)</span>
				</div>
				<div class="feed-coins text-success stat-one col-lg-3 no-padding text-center">
					+<strong title="Chương này đã cộng thêm cho <?php echo $bChap['author']['name'] ?> <?php echo $chapter->coins ?> điểm"><?php echo $chapter->coins ?></strong>
					coins
				</div>
				<div class="feed-share stat-one col-lg-3 no-padding text-right">
				<?php if ($chapter->checkMyShareFB()) echo '<a class="shared"><i class="fa fa-check"></i> <strong id="share_num_chapter_'. $chapter->id .'">'.$bChap['shareNum'] .'</strong> Share</a>';
				else echo '<a class="share" data-param="link='. $chapter->link.'&amp;app_id='. FB_APP_ID .'&amp;redirect_uri='. $chapter->link.'?do=shareFB"><strong id="share_num_chapter_'. $chapter->id .'">'. $bChap['shareNum'] .'</strong> Share</a>'; ?>
				</div>
			</div>
			
			<div class="box-footer box-comments">
<?php 	$ratingsNum = $bChap['ratingsNum'];
		$ratingsList = $bChap['ratingsList'];
		$link = $bChap['link'];
		$parentPageView = $bChap;
		include 'pages/views/_temp/cmtList.feed.php';
		$fType = 'chapter'; $fID = $chapter->id;
		include 'pages/views/_temp/cmtForm.feed.php' ?>
			</div><!-- /.box-footer -->
			
		</div>
		<div class="col-lg-4 no-padding-right feed-wr-book">
			<a title="<?php echo $title ?>" href="<?php echo $link ?>" title="<?php echo $title ?>">
				<img class="book-thumb" src="<?php echo $thumb ?>">
			</a>
<?php if ($book->type == 0) { // is book ?>
			<div class="book-rate">
				<div class="book-score left text-warning">
					<?php echo $book->rAverage ?>
				</div>
				<div class="book-ratings-details">
					<div class="ratings book-ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($book->rAverage > $i && $book->rAverage < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($book->rAverage < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
					</div>
					<a href="<?php echo $book->link.'/reviews' ?>" title="View all <?php echo $book->rTotal ?> reviews" class="gensmall">(<?php echo $book->rTotal ?> reviews)</a>
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
<?php } else { // is topic ?>
			<div class="book-rate">
				<div class="book-score left text-success">
					<?php echo $chapter->countAll() ?>
				</div>
				<div class="book-ratings-details left" style="margin:16px 5px 0">
					<a href="<?php echo $book->link.'/chapters' ?>" style="font-size:18px">bài viết</a>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="book-details no-padding">
				<div class="book-authors">
					<b>Tạo chủ đề:</b> <a href="<?php echo $author['link'] ?>"><?php echo $author['name'] ?></a>
				</div>
				<div class="book-status">
					<b>Tình trạng:</b> <?php echo $sttText ?>
				</div>
			</div>
<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="clearfix"></div>
</div>


