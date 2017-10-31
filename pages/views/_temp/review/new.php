<h2>Đánh giá sách</h2>
<form class="bootstrap-validator-form new-review col-lg-9 no-padding-left" action="<?php echo MAIN_URL ?>/review?do=new">
	<div class="alerts alert-info">
		<b>Chú ý:</b> Chỉ có những cuốn sách <b>đã hoàn thành</b> mới có thể thêm review. <br/>Những cuốn <b>đang tiến hành</b> bạn vui lòng đánh giá (bình luận) theo từng chương.
	</div>
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
				echo '<option value="'.$bO['id'].'">'.$bO['title'].'</option>';
			} ?>
				</optgroup>
				<optgroup label="Chưa được kiểm duyệt">
			<?php foreach ($review->bookList[0] as $bO) {
				echo '<option value="'.$bO['id'].'">'.$bO['title'].'</option>';
			} ?>
				</optgroup>
			</select>
			<a href="#" id="notfindbook" title="Không tìm thấy sách?">Không tìm thấy tên sách bạn muốn review?</a>
			<div class="hide book-name">
				<input type="text" class="form-control" name="book" placeholder="Nhập tên sách vào đây nếu không tìm thấy trong danh sách"/>
			</div>
		<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-lg-3 no-padding-left">Đánh giá (*)</div>
		<div class="col-lg-9 no-padding">
			<div class="star-info rating-icons ratings text-warning text-lg">
			<?php for ($i = 1; $i <= 5; $i++) {
				echo '<i id="'.$i.'" class="fa fa-star-o rating-star-icon v'.$i.'"></i>';
			} ?>
			</div>
			<input type="hidden" name="rate" class="rate-val"/>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group review-content" style="padding-bottom:25px">
		<div class="col-lg-3 control-label no-padding-left">Nội dung (*)</div>
		<div class="col-lg-9 no-padding">
			<textarea class="form-control" name="content"></textarea>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left"></div>
		<div class="col-lg-9 no-padding">
			<label class="checkbox">
				<input type="checkbox" value="true" <?php if ($config->me['oauth_uid']) echo 'checked'; else echo 'disabled' ?> name="to_fb"/> Đăng tải lên Facebook <b class="text-success">+<?php echo COINS_NEW_REVIEW_TO_FACEBOOK ?></b> <span class="gensmall">(Cộng 5 điểm)</span>
				<?php if (!$config->me['oauth_uid']) echo '<div style="margin:-2px 0 0 -20px;font-size:12px">* Chỉ tài khoản đăng nhập bằng Facebook mới có lựa chọn này.</div>' ?>
			</label>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Thumbnail</div>
		<div class="col-lg-9 no-padding">
			<input class="form-control" type="file" name="thumb" id="file" placeholder="Book back" accept="image/*;capture=camera">
			<div style="margin:5px 0">This is neccessary if you choose <b>Upload to Faceboook option.</b></div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 no-padding-left">Status</div>
		<div class="col-lg-9 no-padding">
			<label class="radio col-lg-3">
				<input type="radio" value="1" checked name="status"/> Show
			</label>
			<label class="radio col-lg-3">
				<input type="radio" value="0" name="status"/> Hide
			</label>
		</div>
		<div class="clearfix"></div>
	</div>

	<input type="hidden" name="uid" class="feed-post-by"/>

	<div class="add-form-submit center">
		<input type="reset" value="Reset" class="btn btn-default">
		<input type="submit" value="Submit" class="btn btn-red">
	</div>
</form>
<div class="col-lg-3 no-padding-right feed-rv-book">
	<?php if (isset($bIn)) { ?>
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
	<?php } else echo '<i class="preview-not-avai">Xem trước sách không khả dụng</i>' ?>
</div>
<div class="clearfix"></div>
