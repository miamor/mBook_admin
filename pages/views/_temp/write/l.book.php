		<div class="box-body no-padding-left no-padding-right">
			<div class="col-lg-3 one-book-thumb">
				<img class="book-thumb" src="<?php echo $bO['thumb'] ?>"/>
				<div class="one-book-chapters-num">
					<span><?php echo $bO['chaptersNum'] ?></span> chương
				</div>
			</div>
			<div class="col-lg-9 no-padding-right">
				<h2 class="one-book-title">
					<a href="<?php echo $bO['link'] ?>"><?php echo $bO['title'] ?></a>
				</h2>
				<div class="one-book-des"><?php echo $bO['des'] ?></div>
				<div class="one-book-details">
					<div class="one-book-genres">
						<b>Thể loại:</b> <?php echo $bO['genresText'] ?>
					</div>
					<div class="one-book-status right">
						<b><?php echo $bO['sttText'] ?></b> 
					</div>
					<div class="one-book-author">
						<b>Tác giả:</b> <a href="<?php echo $bO['author']['link'] ?>"><?php echo $bO['author']['name'] ?></a>
					</div>
				</div>
			</div>
		<?php if ($bO['published'] == 1) echo '<div title="Tác phẩm này đã được xuất bản" class="one-book-published"></div>';
		if ($bO['authenticated'] == 1) echo '<div title="Tác phẩm này đã được chuyển sang danh mục Sách" class="one-book-authenticated"></div>' ?>
		</div>
		<div class="box-footer stat feed-sta">
			<div class="feed-ratings stat-one col-lg-6 no-padding">
				<strong class="text-warning"><?php echo $bO['averageRate'] ?></strong>
				<span class="ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($bO['averageRate'] > $i && $bO['averageRate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($bO['averageRate'] < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
				</span>
				<a class="gensmall" href="<?php echo $bO['link'].'/reviews' ?>">(<?php echo $bO['totalReview'] ?> reviews)</a>
			</div>
			<div class="feed-share text-info stat-one col-lg-6 no-padding text-right">
				<a href="#share">
					<strong><?php echo $bO['shareNum'] ?></strong>
					share
				</a>
			</div>
		</div>
