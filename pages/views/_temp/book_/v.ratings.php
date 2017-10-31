		<div class="book-reviews">
		<?php foreach ($ratingsList as $rO) { ?>
			<div class="book-rv-one box">
				<div class="box-header book-rv-user no-padding-bottom">
					<a href="<?php echo $rO['author']['link'] ?>" data-online="<?php echo $rO['author']['online'] ?>" class="left">
						<img class="img-sm img-circle" src="<?php echo $rO['author']['avatar'] ?>">
					</a>
					<div class="left" style="margin-top:2px">
						<a href="<?php echo $rO['author']['link'] ?>">
							<?php echo $rO['author']['name'] ?>
						</a> 
						<span class="ratings text-warning">
							<?php for ($i = 1; $i <= 5; $i++) {
								if ($rO['rate'] > $i && $rO['rate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
								else if ($rO['rate'] < $i) echo '<i class="fa fa-star-o"></i>';
								else echo '<i class="fa fa-star"></i>';
							} ?>
						</span>
					</div>
					<div class="coins-plus right" title="Review của <?php echo $rO['author']['name'] ?> được đánh giá <?php echo $rO['coins'] ?> điểm">
						<span class="text-success">+<?php echo $rO['coins'] ?></span>
					</div>
				</div>
				<div class="box-body">
					<?php echo $rO['short_content'] ?>
				</div>
				<!--<div class="box-footer box-comments">
					Xem 5 bình luận cho đánh giá này
				</div> -->
			</div>
		<?php }
			if ($ratingsNum > 5) echo '<a href="'.$link.'/reviews" class="btn btn-red btn-block">Xem tất cả '.$ratingsNum.' đánh giá</a>'; ?>
		</div>
