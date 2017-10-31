<h2>Edit donation: <?php echo $user['name'].' - '.$book['title'] ?></h2>
<form class="bootstrap-validator-form new-write" action="?type=storage&mode=edit&id=<?php echo $_id ?>&do=edit">
	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Uid</div>
		<div class="col-lg-9 no-padding">
			<select name="uid" class="form-control chosen-select">
		<?php foreach ($uList as $aO) {
				if ($aO['id'] == $uid) $selected = 'selected';
				else $selected = '';
				echo '<option '.$selected.' value="'.$aO['id'].'">'.$aO['name'].'</option>';
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
			if ($bO['id'] == $bid) $selected = 'selected';
			else $selected = '';
			echo '<option '.$selected.' value="'.$bO['id'].'">'.$bO['title'].'</option>';
		} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<div class="col-lg-3 no-padding-left">Quanity</div>
		<div class="col-lg-9 no-padding">
			<input type="number" class="form-control" min="1" value="<?php echo $num ?>" name="num"/>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="add-form-submit center">
		<input type="reset" value="Reset" class="btn btn-default">
		<input type="submit" value="Submit" class="btn btn-red">
	</div>
</form>
