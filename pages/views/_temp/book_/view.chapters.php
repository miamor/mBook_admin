<div class="book-info">
	<div class="col-lg-12 no-padding">
		<h2 class="book-title">
			<?php echo $title ?>
		</h2>
		<div class="col-lg-3 book-left-col no-padding-left">
			<?php include 'v.sidebar.php' ?>
		</div>
		<div class="col-lg-9 book-chapters-list">
			<div class="mode-btns right">
				<a class="btn btn-default pull-right" href="?mode=newchapter"><span class="fa fa-plus"></span> Thêm chương mới</a>
			</div>
			<?php if (count($bChapters) > 0) include 'v.chapters.php';
			else echo '<div class="alerts alert-info">Không có danh sách chương.</div>' ?>
		</div> 
		<div class="clearfix"></div>
	</div>
	
	<div class="clearfix"></div>
</div>

