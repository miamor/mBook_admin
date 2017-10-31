<div class="book-info">
	<div class="col-lg-9 no-padding">
		<div class="mode-btns right">
		<?php if ($page == 'write') { ?>
			<a class="btn btn-default pull-right" href="<?php echo $config->wLink ?>?mode=new"><span class="fa fa-plus"></span> Chủ đề mới</a>
		<?php }
		if ($uid === $config->u || $config->me['is_mod'] === 1) { ?>
			<a class="btn btn-default pull-right" href="<?php echo str_replace('/book/', '/write/', $link) .'/?mode=edit' ?>"><span class="fa fa-pencil"></span> Sửa</a>
		<?php } ?>
		</div>
		<h2 class="book-title">
			<?php echo $title ?>
		</h2>
		<div class="col-lg-<?php echo ($ratingsNum > 0) ? 4 : 3 ?> book-left-col no-padding-left">
			<?php include 'v.sidebar.php'; ?>
		</div>
		<div class="col-lg-<?php echo ($ratingsNum > 0) ? 8 : 9 ?> no-padding">
			<div class="box book-des">
				<h3 class="box-header with-border no-margin">Lời dẫn</h3>
				<div class="box-body">
					<?php echo $des ?>
				</div>
			</div>
		<?php if ($quotesNum > 0) { ?>
			<div class="book-quote">
				<h3>Trích dẫn hay</h3>
			<?php foreach ($quotesAr as $qO) {
				echo '<blockquote class="blockquote">'.$qO.'</blockquote>';
			} ?>
			</div>
		<?php } ?>

		</div>
		<div class="clearfix"></div>

	</div>

	<div class="col-lg-3 no-padding-right topic-posts">
		<?php include 'v.chapters.php'; ?>
	</div>
	
	<div class="clearfix"></div>
</div>

