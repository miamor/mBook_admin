<div class="book-info">
	<div class="col-lg-1"></div>
	<div class="col-lg-10 no-padding">
		<h2 class="book-title">
			<a href="<?php echo $link ?>"><?php echo $title ?></a>
		</h2>
	<?php if (!$bChap['id']) echo '<div class="alerts alert-warning">Không tìm thấy chương.</div>';
	else { ?>
		<div class="box">
			<div class="box-header feed-main-head feed-wr-head">
			<?php if ($bChap['uid'] === $config->u) { ?>
				<div class="pull-right">
					<a class="text-muted" href="<?php echo str_replace('/book/', '/write/', $bChap['link']) .'/?mode=editchapter' ?>" title="Sửa chương này"><span class="fa fa-pencil"></span></a>
				</div>
			<?php } ?>
				<a class="img-circle left" title="<?php echo $bChap['author']['name'] ?>" href="<?php echo $bChap['author']['link'] ?>" data-online="<?php echo $bChap['author']['online'] ?>">
					<img class="img-sm img-circle" src="<?php echo $bChap['author']['avatar'] ?>">
				</a>

				<h3 class="book-chapter-title"><?php echo $bChap['title'] ?></h3>
			</div>
			<div class="box-body feed-main-content feed-wr-content">
				<div class="chapter-content">
					<?php echo $bChap['content'] ?>
				</div>
			</div>

			<div class="box-body feed-time">
				<?php echo $bChap['created'] ?>
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

		</div> <!-- .box -->
	<?php } ?>

	</div>
	
	<div class="col-lg-1"></div>

	<div class="clearfix"></div>

	<?php if ($bChap['id']) { ?>
		<div class="col-lg-1"></div>
		<div class="nav-tabs-customs col-lg-10" id="comments">
			<h3>Bình luận</h3>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#mbook" data-toggle="tab">mBook</a></li>
				<li><a href="#fb" data-toggle="tab">Facebook</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="mbook">
					<div clas="r-cmts hide" style="margin-top:20px">
						<table class="etable review-comments" id="r_comments">
							<thead class="hidden">
								<tr>
									<th class="th-none hidden"></th>
									<th class="th-none hidden"></th>
									<th class="th-none hidden"></th>
								</tr>
							</thead>
						</table> <!-- /.review-comments #r_comments -->
					</div>
					<?php include 'pages/views/_temp/cmtForm.php' ?>
				</div>
				<div class="tab-pane" id="fb">
					<div id="fb-root"></div>
					<div class="fb-comments" data-href="<?php echo $config->currentURL ?>" data-width="950" data-numposts="5"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-1"></div>
		<div class="clearfix"></div>
	<?php } ?>

</div>

