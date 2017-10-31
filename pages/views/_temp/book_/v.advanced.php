<h3>Tìm mua sách</h3>

<table id="books_publisher" class="box table table-bordered table-striped">
	<thead>
		<tr>
			<th></th>
			<th>Địa chỉ</th>
			<th class="text-center">Giá tiền</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($booksBuyList as $oS) { ?>
		<tr valign="middle">
			<td class="col-lg-3">
				<img style="margin-right:10px" class="local-book-thumb left" src="<?php echo $oS['thumb'] ?>"/>
				<a href="<?php echo $oS['sIn']['url'] ?>">
					<div><?php echo $oS['sIn']['title'] ?></div>
					<img class="local-store-thumb" src="<?php echo $oS['sIn']['thumb'] ?>"/>
				</a>
			</td>
			<td class="col-lg-7">
				<ol class="local-address">
			<?php foreach ($oS['sIn']['address'] as $oneAdd) 
					echo '<li>'.rtrim($oneAdd).'</li>' ?>
				</ol>
			</td>
			<td class="col-lg-2 text-center" valign="middle">
				<?php echo $oS['price'] ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

