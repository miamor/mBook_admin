<h2>List</h2>

<form id="formFilter" class="filterBox filters">
	<h3 class="filterBox-header">Filter</h3>
	<div class="book-search form-group">
		<div class="col-lg-4 control-label text-right">Uid</div>
		<div class="col-lg-6">
			<select name="uid[]" multiple class="form-control chosen-select">
		<?php foreach ($uList as $aO) {
			echo '<option value="'.$aO['id'].'">'.$aO['name'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="filter-author form-group" title="Book">
		<div class="col-lg-4 control-label text-right">Book</div>
		<div class="col-lg-8">
			<label class="radio col-lg-5">
				<input type="radio" value="1" checked name="avai"/> In mBook library
			</label>
			<label class="radio col-lg-5">
				<input type="radio" value="0" name="avai"/> Not available yet
			</label>
		</div>
		<div class="clearfix"></div>

		<div class="col-lg-4 control-label text-right"></div>
		<div class="col-lg-6">
			<select name="bid[]" multiple class="select-bookid form-control chosen-select">
		<?php foreach ($bList as $bO) {
			echo '<option value="'.$bO['id'].'">'.$bO['title'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="filter-status form-group">
		<div class="col-lg-4 text-right">Status</div>
		<div class="col-lg-6">
			<label class="radio col-lg-3">
				<input type="radio" value="1" name="status"/> Show
			</label>
			<label class="radio col-lg-3">
				<input type="radio" value="0" name="status"/> Hide
			</label>
			<label class="radio col-lg-3">
				<input type="radio" value="-1" checked name="status"/> All
			</label>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="center">
		<input type="submit" value="Lọc"/>
	</div>
</form>

<div class="">
	<div id="book-list"></div>
</div>

<div class="clearfix"></div>
