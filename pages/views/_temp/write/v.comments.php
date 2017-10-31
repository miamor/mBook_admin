		<div class="book-reviews">
		<?php for ($i = 0; $i < 5; $i++) { ?>
			<div class="book-rv-one box">
				<div class="box-header book-rv-user no-padding-bottom">
					<a href="#" data-online="1" class="left">
						<img class="img-sm img-circle" src="http://localhost/mRoom/data/img/7.jpg">
					</a>
					<div class="left" style="margin-top:3px">
						<a href="#">Tu Nguyen</a> 
					</div>
				</div>
				<div class="box-body">
					A book review is a descriptive and critical/evaluative account of a book. It provides a summary of the content, assesses the value of the book, and recommends it (or not) to other potential readers.<br/>
A book report is an objective summary of the ma... <a href="<?php echo MAIN_URL ?>/review/1" id="<?php echo $i ?>" class="book-rv-read gensmall">See more</a>
				</div>
			</div>
		<?php } ?>
			<a class="btn btn-red btn-block">Xem tất cả 10 bình luận</a>
		</div>
