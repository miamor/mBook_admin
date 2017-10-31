<h2>Đánh giá sách: <?php echo ($book['id'] ? $book['title'] : $title) ?></h2>
<form class="bootstrap-validator-form new-review col-lg-9 no-padding-left" action="?do=edit">
	<div class="form-group book-title-group">
		<div class="col-lg-3 control-label no-padding-left">Tên sách (*)</div>
		<div class="col-lg-9 no-padding">
		<?php if (isset($bIn)) { ?>
			<input type="text" class="form-control" readonly name="book" value="<?php echo $bIn['title'] ?>"/>
			<input type="hidden" value="<?php echo $bIn['id'] ?>" name="bid"/>
		<?php } else { ?>
			<select class="form-control book-select chosen-select" name="bid">
				<option value="0" selected>--- Chọn từ danh sách có trong mBook ---</option>
				<optgroup label="Đã được kiểm duyệt">
			<?php foreach ($review->bookList[1] as $bO) {
				if ($book['id'] == $bO['id']) $selected = 'selected';
				else $selected = '';
				echo '<option '.$selected.' value="'.$bO['id'].'">'.$bO['title'].'</option>';
			} ?>
				</optgroup>
				<optgroup label="Chưa được kiểm duyệt">
			<?php foreach ($review->bookList[0] as $bO) {
				if ($book['id'] == $bO['id']) $selected = 'selected';
				else $selected = '';
				echo '<option '.$selected.' value="'.$bO['id'].'">'.$bO['title'].'</option>';
			} ?>
				</optgroup>
			</select>
			<a href="#" id="notfindbook" title="Không tìm thấy sách?">Không tìm thấy tên sách bạn muốn review?</a>
			<div class="<?php if ($book['id']) echo 'hide' ?> book-name">
				<input type="text" class="form-control" name="book" placeholder="Nhập tên sách vào đây nếu không tìm thấy trong danh sách" value="<?php if (!$book['id']) echo $title ?>"/>
			</div>
		<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 no-padding-left">Đánh giá</div>
		<div class="col-lg-9 no-padding">
			<div class="star-info rating-icons ratings text-warning text-lg">
			<?php for ($i = 1; $i <= 5; $i++) {
				if ($i <= $rate) echo '<i id="'.$i.'" class="fa fa-star active rating-star-icon v'.$i.'"></i>';
				else echo '<i id="'.$i.'" class="fa fa-star-o rating-star-icon v'.$i.'"></i>';
			} ?>
			</div>
			<input type="hidden" name="rate" class="rate-val" value="<?php echo $rate ?>"/>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Nội dung</div>
		<div class="col-lg-9 no-padding">
			<textarea class="form-control" name="content"><?php echo $content ?></textarea>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 no-padding-left">Status</div>
		<div class="col-lg-9 no-padding">
			<label class="radio col-lg-3">
				<input type="radio" value="1" <?php if ($show == 1) echo 'checked' ?> name="status"/> Show
			</label>
			<label class="radio col-lg-3">
				<input type="radio" value="0" <?php if ($show == 0) echo 'checked' ?> name="status"/> Hide
			</label>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="add-form-submit center">
		<input type="reset" value="Reset" class="btn btn-default">
		<input type="submit" value="Submit" class="btn btn-red">
	</div>
</form>

<div class="col-lg-3 no-padding-right feed-rv-book">
	<?php if ($book['id']) { ?>
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
					<div class="book-genres">
						<b>Tác giả:</b> <a href="<?php echo $book['author']['link'] ?>"><?php echo $book['author']['name'] ?></a>
					</div>
				</div>
			</div>
	<?php } ?>
</div>
<div class="clearfix"></div>
