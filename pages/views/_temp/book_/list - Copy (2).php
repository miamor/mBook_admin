<form class="col-lg-2 filters no-padding">
	<h3>Lọc kết quả</h3>
	<div class="filter-author-type">
		<h4 class="filter-header with-border">Author type</h4>
		<div class="filter-body">
			<label class="checkbox">
				<input type="checkbox" value="mbook" checked name="auth_type"/> mBook members
			</label>
			<label class="checkbox">
				<input type="checkbox" value="others" checked name="auth_type"/> Others
			</label>

		</div>
	</div>
	<div class="filter-author-type">
		<h4 class="filter-header with-border">Free download</h4>
		<div class="filter-body">
			<label class="radio">
				<input type="radio" value="0" checked name="free_download"/> Everything
			</label>
			<label class="radio">
				<input type="radio" value="1" checked name="free_download"/> With free samples
			</label>

		</div>
	</div>
</form>

<div class="col-lg-10 no-padding-right">
<table id="book-list" class="book-list">
<thead class="hidden">
	<tr>
		<th class="hidden th-none"></th>
		<th class="hidden th-none"></th>
	</tr>
</thead>
<tbody>
<?php foreach ($_List as $bK => $bO) { ?>
<tr class="col-lg-2">
	<td>
		<div class="book-img">
			<img class="book-thumb" src="<?php echo $bO['thumb'] ?>"/>
			<div class="book-chapters-num">
				<span><?php echo $bO['chaptersNum'] ?></span> chương
			</div>
		</div>
		<div class="book-info">
            <a href="<?php echo $bO['link'] ?>" title="<?php echo $bO['title'] ?>">
                <h3 class="book-title"><?php echo $bO['title'] ?></h3>
            </a>
            <div class="book-authors">
                <a class="author-detail hightlight click_author_info" href="<?php echo $bO['author']['link'] ?>"><?php echo $bO['author']['name'] ?></a>
			</div>
        </div>
	</td>
</tr>
<!--<tr class="col-lg-6">
	<td class="hidden"><?php echo $bO['last_chapter'] ?></td>
	<td data-published="<?php echo $bO['published'] ?>" data-uid="<?php echo ($bO['uid']) ? 1 : 0 ?>" class="box one-book">
		<div class="box-body">
			<div class="col-lg-3 one-book-thumb">
				<img class="book-thumb" src="<?php echo $bO['thumb'] ?>"/>
				<div class="one-book-chapters-num">
					<span><?php echo $bO['chaptersNum'] ?></span> chương
				</div>
			</div>
			<div class="col-lg-9 no-padding-right">
				<h2 class="one-book-title">
					<a title="<?php echo $bO['title'] ?> - <?php echo $bO['created'] ?>" href="<?php echo $bO['link'] ?>">
						<?php echo $bO['title'] ?>
					</a>
				</h2>
				<div class="one-book-des"><?php echo $bO['des'] ?></div>
				<div class="one-book-details">
					<div class="one-book-genres">
						<b>Thể loại:</b> <?php echo $bO['genresText'] ?>
					</div>
					<div class="one-book-status right">
						<b><?php echo $bO['sttText'] ?></b> 
					</div>
					<div class="one-book-author">
						<b>Tác giả:</b> <a href="<?php echo $bO['author']['link'] ?>"><?php echo $bO['author']['name'] ?></a>
					</div>
				</div>
			</div>
		<?php if ($bO['published'] == 1) echo '<div title="Tác phẩm này đã được xuất bản" class="one-book-published"></div>';
		if ($bO['uid']) echo '<div title="Tác phẩm này được viết bởi thành viên của mBook" class="one-book-written"></div>' ?>
		</div>
		<div class="box-footer stat feed-sta">
			<div class="feed-ratings stat-one col-lg-6 no-padding">
				<strong class="text-warning"><?php echo $bO['averageRate'] ?></strong>
				<span class="ratings text-warning">
					<?php for ($i = 1; $i <= 5; $i++) {
						if ($bO['averageRate'] > $i && $bO['averageRate'] < ($i+1)) echo '<i class="fa fa-star-half-o"></i>';
						else if ($bO['averageRate'] < $i) echo '<i class="fa fa-star-o"></i>';
						else echo '<i class="fa fa-star"></i>';
					} ?>
				</span>
				<a class="gensmall" href="<?php echo $bO['link'].'/reviews' ?>">(<?php echo $bO['totalReview'] ?> reviews)</a>
			</div>
			<div class="feed-share text-info stat-one col-lg-6 no-padding text-right">
				<a href="#share">
					<strong><?php echo $bO['shareNum'] ?></strong>
					share
				</a>
			</div>
		</div>
	</td>
</tr>-->
<?php } ?>
</tbody>
</table>
</div>

<div class="clearfix"></div>
