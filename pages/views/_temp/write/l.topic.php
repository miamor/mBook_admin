<div class="topic-bg" style="background-image:url(<?php echo $bO['thumb'] ?>)"></div>
		<div class="box-body no-padding-left no-padding-right">
			<div class="col-lg-12 no-padding">
				<h2 class="one-book-title">
					<a href="<?php echo $bO['link'] ?>"><?php echo $bO['title'] ?></a>
				</h2>
				<div class="one-book-des"><?php echo $bO['des'] ?></div>
				<div class="one-book-details">
					<div class="one-book-status">
						<b>Tình trạng:</b> <b><?php echo $bO['sttText'] ?></b> 
					</div>
					<div class="one-book-author">
						<b>Người khởi tạo:</b> <a href="<?php echo $bO['author']['link'] ?>"><?php echo $bO['author']['name'] ?></a>
					</div>
				</div>
			</div>
		<?php if ($bO['published'] == 1) echo '<div title="Tác phẩm này đã được xuất bản" class="one-book-published"></div>';
		if ($bO['authenticated'] == 1) echo '<div title="Tác phẩm này đã được chuyển sang danh mục Sách" class="one-book-authenticated"></div>' ?>
		</div>
		<div class="box-footer stat feed-sta">
			<div class="feed-share text-info stat-one col-lg-12 no-padding text-right">
				<a href="#share">
					<strong><?php echo $bO['shareNum'] ?></strong>
					share
				</a>
			</div>
		</div>
