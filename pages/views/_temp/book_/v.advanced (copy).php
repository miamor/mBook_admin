<h3>Tìm mua sách</h3>

<div class="nav-tabs-customs">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#offline" data-toggle="tab">Địa chỉ mua offline</a></li>
		<li><a href="#online" data-toggle="tab">Tìm mua online</a></li>
	</ul>
	<div class="tab-content buy-books">
		<div class="tab-pane active" id="offline">
<table id="books_publisher" class="box table table-bordered table-striped">
	<thead>
		<tr>
			<th class="hidden th-none"></th>
			<th>Địa chỉ</th>
			<th>Xem trước</th>
			<th class="text-center">Giá tiền</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($publishedList as $oP) { ?>
		<tr>
			<td class="hidden">
				<?php echo $oP['published_day'] ?>
			</td>
			<td class="col-lg-4">
				<a href="<?php echo $oP['author']['link'] ?>">
					<?php echo $oP['author']['title'] ?>
				</a>
			</td>
			<td class="preview col-lg-6">
				<div class="col-lg-2 no-padding">
					<img class="book-thumb" src="<?php echo $oP['thumb'] ?>"/>
				</div>
				<div class="col-lg-10 no-padding-right">
					<div class="book-published-content">
						<?php echo $oP['des'] ?>
					</div>
					<div class="book-buy-detail">
						<a class="btn btn-red btn-sm" href="<?php echo $oP['link'] ?>">Details</a>
					</div>
				</div>
			</td>
			<td class="col-lg-2">
				<?php echo $oP['price'] ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
		</div><!-- #offline -->
		
		<div class="tab-pane" id="online">
		</div><!-- #online -->
	</div><!-- .tab-content -->
</div>
