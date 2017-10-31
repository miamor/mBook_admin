<div class="book-chapter">
<?php 
if ( ($page == 'write' && ($status == 0 || $uid === $config->u) ) || ($page == 'book' && ($uid === $config->u || $config->me['is_mod'] === 1) ) ) 
	echo '<a class="btn btn-default pull-right" style="margin-top:-8px" href="?mode=newchapter"><span class="fa fa-pencil"></span> Thêm bài viết mới</a><div class="clearfix"></div>';
if (count($bChapters) > 0) { ?>
	<h3 class="no-margin-top">
		<a href="<?php echo $link.'/chapters' ?>"><?php echo ($page == 'book') ? 'Danh sách chương' : 'Bài viết' ?></a>
	</h3>
	<div class="clearfix"></div>
	<table id="books_chapters" class="box table table-border-wrap table-striped">
			<thead>
				<tr>
					<th class="hidden"></th>
					<th></th>
					<th>Chương</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($bChapters as $cO) { ?>
				<tr>
					<td class="hidden"><?php echo $cO['created'].' - '.$cO['id'] ?></td>
					<td style="width:65px;padding-left:15px">
						<div class="coins-plus" title="Chương này được đánh giá <?php echo $cO['coins'] ?> điểm">
							<span class="text-success">+<?php echo $cO['coins'] ?></span>
						</div>
					</td>
					<td>
						<a title="<?php echo $cO['title'] ?>" href="<?php echo $cO['link'] ?>">
							<?php echo $cO['title'] ?>
						</a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
	</table>
<?php } else echo '<div class="alerts alert-info">Không có danh sách chương.</div>';
?>
</div>
