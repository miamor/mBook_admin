<?php if (count($requests) > 0) { ?>
<h2><?php echo count($requests) ?> người đã đề nghị xuất bản cuốn sách này</h2>
<table id="books_request_publish" class="box table table-border-wrap table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>User</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($requests as $rk => $uRq) { ?>
	<tr>
		<td style="width:45px;padding-left:15px;vertical-align:middle">
			<span class="small"><?php echo $rk+1 ?></span>
		</td>
		<td>
			<a class="img-circle left" href="<?php echo $uRq['link'] ?>" data-online="<?php echo $uRq['online'] ?>">
				<img class="img-sm img-circle" src="<?php echo $uRq['avatar'] ?>">
			</a>
			<a style="display:block;margin-top:4px" href="<?php echo $uRq['link'] ?>"><?php echo $uRq['name'] ?></a>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>

<?php } ?>
