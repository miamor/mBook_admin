<h2>Add borrow request</h2>
<form class="bootstrap-validator-form new-write" action="?type=borrow&mode=add&do=add">
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
		<div class="col-lg-3 no-padding-left">Status</div>
		<div class="col-lg-9 no-padding">
			<label class="radio col-lg-3">
				<input type="radio" value="1" name="stt"/> Returned
			</label>
			<label class="radio col-lg-3">
				<input type="radio" value="1" name="stt"/> Keeping
			</label>
			<label class="radio col-lg-3">
				<input type="radio" value="0" checked name="stt"/> Waiting...
			</label>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="add-form-submit center">
		<input type="reset" value="Reset" class="btn btn-default">
		<input type="submit" value="Submit" class="btn btn-red">
	</div>
</form>
