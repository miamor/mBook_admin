<h2><?php echo $page_title ?></h2>
<form class="bootstrap-validator-form new-write" action="?do=editchapter">
	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Tiêu đề</div>
		<div class="col-lg-9 no-padding">
			<input class="form-control" name="title" value="<?php echo $bChap['title'] ?>" type="text"/>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-lg-3 control-label no-padding-left">Nội dung</div>
		<div class="col-lg-9 no-padding">
			<textarea class="form-control" name="content"><?php echo $bChap['content'] ?></textarea>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="add-form-submit center">
		<input type="reset" value="Reset" class="btn btn-default">
		<input type="submit" value="Submit" class="btn btn-red">
	</div>
</form>
