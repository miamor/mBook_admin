<h2>List</h2>

<form id="formFilter" class="filterBox filters">
	<h3 class="filterBox-header">Filter</h3>
	<div class="book-search form-group" id="bsearch">
		<div class="col-lg-4 control-label text-right">Keyword</div>
		<div class="col-lg-6">
			<input class="form-control btit" type="text" id="book-search-title" name="keyword" autocomplete="off" placeholder="Search by title"/>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="filter-genres form-group">
		<div class="col-lg-4 control-label text-right">Genres</div>
		<div class="col-lg-6">
			<select name="genres[]" multiple class="form-control chosen-select" title="Genres">
		<?php foreach ($genList as $gO) {
			echo '<option value="'.$gO['id'].'">'.$gO['title'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="filter-author form-group" title="Author">
		<div class="col-lg-4 control-label text-right">Author</div>
		<div class="col-lg-6">
			<select name="author[]" multiple class="form-control chosen-select">
		<?php foreach ($auList as $aO) {
			echo '<option value="'.$aO['name'].'">'.$aO['name'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="filter-status form-group">
		<div class="col-lg-4 text-right">Type</div>
		<div class="col-lg-6">
			<label class="radio col-lg-5">
				<input type="radio" value="1" name="in_storage"/> In mBook storage
			</label>
			<label class="radio col-lg-5">
				<input type="radio" value="-1" checked name="in_storage"/> All
			</label>
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
