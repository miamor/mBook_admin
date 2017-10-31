<h2>Add donation</h2>
<form class="bootstrap-validator-form new-write" action="?type=storage&mode=add&do=add">
	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Uid</div>
		<div class="col-lg-9 no-padding">
			<select name="uid" class="form-control chosen-select">
		<?php foreach ($uList as $aO) {
			echo '<option value="'.$aO['id'].'">'.$aO['name'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Book</div>
		<div class="col-lg-9 no-padding">
			<select name="bid" class="form-control chosen-select">
		<?php foreach ($bList as $bO) {
			echo '<option value="'.$bO['id'].'">'.$bO['title'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 no-padding-left">Quanity</div>
		<div class="col-lg-9 no-padding">
			<input type="number" class="form-control" min="1" name="num"/>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="add-form-submit center">
		<input type="reset" value="Reset" class="btn btn-default">
		<input type="submit" value="Submit" class="btn btn-red">
	</div>
</form>
