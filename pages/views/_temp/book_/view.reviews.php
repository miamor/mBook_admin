<div class="book-info">
<?php if (!$temp) { ?>
		<h2 class="book-title">
			<?php echo $title ?>
		</h2>
		<div class="col-lg-3 book-left-col no-padding-left">
			<?php include 'v.sidebar.php' ?>
		</div>
<?php } ?>
	<div class="<?php if (!$temp) echo 'col-lg-9 no-padding' ?> book-reviews-list">

	<div class="book-reviews">
	
<?php if (count($ratingsList) <= 0) echo '<div class="alerts alert-info">Chưa có đánh giá nào.</div>';
else { ?>
<table id="book-reviews">
<thead class="hidden">
	<tr>
		<th></th>
		<th></th>
		<th></th>
	</tr>
</thead>
<tbody>
<?php foreach ($ratingsList as $rO) { ?>
<tr class="feed-one-item feed-item-review">
	<td class="hidden"><?php echo $rO['created'].' - '.$rO['id'] ?></td>
	<td valign="top" class="feed-user-info col-lg-1 no-padding centered <?php if ($temp) echo 'hidden' ?>">
		<a title="<?php echo $rO['author']['name'] ?>" href="<?php echo $rO['author']['link'] ?>" data-online="<?php echo $rO['author']['online'] ?>">
			<img class="feed-user-avt img-circle" src="<?php echo $rO['author']['avatar'] ?>">
		</a>
	</td>
	<td class="feed-content col-lg-11 no-padding feed-rv">
		<div class="box box-review feed-main feed-rv-main">
			<div class="box-header feed-main-head feed-rv-head">
				<div class="feed-ratings right ratings text-lg text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($rO['rate'] > $i && $rO['rate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($rO['rate'] < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
				</div>
				<?php if ($temp) echo '<img class="feed-user-avt img-circle img-mini left" src="'. $rO['author']['avatar'] .'"/>' ?>
				<a href="<?php echo $rO['author']['link'] ?>"><?php echo $rO['author']['name'] ?></a> đã thêm 1 <a href="<?php echo $rO['link'] ?>">review</a> cho cuốn sách <a href="<?php echo $link ?>"><?php echo $title ?></a>
			</div>
			<div class="box-body feed-main-content feed-rv-content">
				<?php echo $rO['content_feed'] ?>
			</div>

			<div class="box-footer stat feed-sta">
				<div class="feed-ratings stat-one col-lg-8 no-padding">
					<strong class="text-warning"><?php echo $rO['average'] ?></strong>
					<span class="ratings text-warning">
						<?php for ($i = 1; $i <= 5; $i++) {
							if ($rO['average'] > $i && $rO['average'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
							else if ($rO['average'] < $i) echo '<i class="fa fa-star-o"></i>';
							else echo '<i class="fa fa-star"></i>';
						} ?>
					</span>
					<a href="<?php echo $rO['link'] ?>" class="small">(<?php echo $rO['total'] ?> ratings)</a>
				</div>
				<div class="feed-coins text-success stat-one col-lg-4 no-padding text-center">
					+<strong><?php echo $rO['coins'] ?></strong>
					coins
				</div>
				<div class="feed-share text-info stat-one col-lg-3 no-padding text-right hidden">
					<a href="#share">
						<strong><?php echo $rO['shareNum'] ?></strong>
						share
					</a>
				</div>
			</div>
			
		<?php if ($rO['ratingsNum'] > 0) { ?>
			<div class="box-footer box-comments">
			<?php foreach ($rO['ratingsList'] as $r_rO) { ?>
				<div class="box-comment">
					<div class="box-comment-left">
						<a href="<?php echo $r_rO['author']['link'] ?>" data-online="<?php echo $r_rO['author']['online'] ?>" class="left">
							<img class="img-sm img-circle" src="<?php echo $r_rO['author']['avatar'] ?>">
						</a>
					</div>
					<div class="comment-text">
						<span class="username">
							<a href="<?php echo $r_rO['author']['link'] ?>"><?php echo $r_rO['author']['name'] ?></a>
							<span class="ratings text-warning">
							<?php for ($i = 1; $i <= 5; $i++) {
								if ($r_rO['rate'] > $i && $r_rO['rate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
								else if ($r_rO['rate'] < $i) echo '<i class="fa fa-star-o"></i>';
								else echo '<i class="fa fa-star"></i>';
							} ?>
							</span>
							<span class="coins-plus" title="Review của <?php echo $r_rO['author']['name'] ?> đã cộng thêm cho <?php echo $rO['author']['name'] ?> <?php echo $r_rO['coins'] ?> điểm">
								<span class="text-success">+<?php echo $r_rO['coins'] ?></span>
							</span>
							<span class="text-muted pull-right"><?php echo $r_rO['created'] ?></span>
						</span><!-- /.username -->
						<?php echo $r_rO['content'] ?>
					</div><!-- /.comment-text -->
				</div><!-- /.box-comment -->
			<?php } ?>
			</div><!-- /.box-footer -->
		<?php } ?>
		</div> <!-- .feed-rv-main -->
	</td> <!-- .feed-rv -->
	
</tr> <!-- .feed-one-item -->
<?php } ?>
</tbody>
</table> <!-- #book-reviews -->

<?php } ?>

	</div> <!-- .book-reviews -->

	</div> <!-- .col-lg-9 .book-reviews-list -->
	<div class="clearfix"></div>
</div>
