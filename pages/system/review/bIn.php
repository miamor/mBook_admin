<?php
	$bookID = $config->get('bid');
	$bIn = $review->getBookInfo($bookID);
?>
<a href="<?php echo $bIn['link'] ?>" title="<?php echo $bIn['title'] ?>">
	<img class="book-thumb" src="<?php echo $bIn['thumb'] ?>">
</a>
<div class="book-rate">
	<div class="book-score left text-warning">
		<?php echo $bIn['averageRate'] ?>
	</div>
	<div class="book-ratings-details">
		<div class="ratings book-ratings text-warning">
		<?php for ($i = 1; $i <= 5; $i++) {
if ($bIn['averageRate'] > $i && $bIn['averageRate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
else if ($bIn['averageRate'] < $i) echo '<i class="fa fa-star-o"></i>';
else echo '<i class="fa fa-star"></i>';
		} ?>
		</div>
		<a href="<?php echo $bIn['link'].'/reviews' ?>" title="View all <?php echo $bIn['totalReview'] ?> reviews" class="gensmall">(<?php echo $bIn['totalReview'] ?> reviews)</a>
	</div>
</div>
<div class="book-details no-padding">
	<div class="">
		<div class="book-genres">
			<b>Thể loại:</b> <?php echo $bIn['genresText'] ?>
		</div>
		<div class="book-genres">
			<b>Tác giả:</b> <a href="<?php echo $bIn['author']['link'] ?>"><?php echo $bIn['author']['name'] ?></a>
		</div>
	</div>
</div>
