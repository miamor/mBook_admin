<?php //echo date("Y-m-d H:i:s");
$config->addJS('plugins', 'bootstrapValidator/bootstrapValidator.min.js');
$config->addJS('plugins', 'sceditor/minified/jquery.sceditor.min.js');
$config->addJS('dist', 'main.js'); ?>
<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo IMG ?>/b.jpg" />

	<title><?php echo $page_title ?></title>

	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="<?php echo MAIN_URL ?>/assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo CSS ?>/font.min.css">
	<!--<link rel="stylesheet" href="<?php echo CSS ?>/style.min.old.css">-->
	<link rel="stylesheet" href="<?php echo CSS ?>/plugins.css">
	<!-- Page style CSS
	<link rel="stylesheet" href="<?php echo CSS ?>/admin.min.css"> -->
	<link rel="stylesheet" href="<?php echo CSS ?>/light.css">

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="<?php echo MAIN_URL ?>/assets/jquery/jquery-2.2.3.min.js"></script>
<!--	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script> -->

	<!-- Latest compiled and minified JavaScript -->
	<script src="<?php echo MAIN_URL ?>/assets/bootstrap/js/bootstrap.min.js"></script>
	<script>var MAIN_URL = '<?php echo MAIN_URL ?>' </script>

</head>
<body>

<header>
	<div class="header_top">
		mBook admin
	</div>
	<nav id="top_navbar" class="navbar navbar-static-top">
			<ul class="items-list">
				<li class="one-item <?php if ($page == 'home') echo 'active' ?>" id="home"><a href="<?php echo MAIN_URL ?>">Feed</a></li>
				<li class="one-item <?php if ($page == 'book') echo 'active' ?>" id="book">
					<a href="<?php echo MAIN_URL ?>/book">Library</a>
				</li>
				<li class="one-item <?php if ($page == 'review') echo 'active' ?>" id="review">
					<a href="<?php echo MAIN_URL ?>/review">Review</a>
				</li>
				<li class="one-item <?php if ($page == 'user') echo 'active' ?>" id="user">
					<a href="<?php echo MAIN_URL ?>/user">User</a>
				</li>
				<!--<li class="one-item <?php if ($page == 'write') echo 'active' ?>" id="write">
					<a href="<?php echo MAIN_URL ?>/write">Viết</a>
				</li>
				<li class="one-item <?php if ($page == 'summary') echo 'active' ?>" id="summary">
					<a href="<?php echo MAIN_URL ?>/summary">Tóm tắt sách</a>
				</li>
				<li class="one-item <?php if ($page == 'author') echo 'active' ?>" id="author">
					<a href="<?php echo MAIN_URL ?>/author">Tác giả</a>
				</li>
				<li class="one-item <?php if ($page == 'review') echo 'active' ?>" id="review">
					<a href="<?php echo MAIN_URL ?>/review">Đánh giá</a>
				</li>
				<li class="one-item <?php if ($page == 'gift') echo 'active' ?>" id="gift">
					<a href="<?php echo MAIN_URL ?>/gift">Quà tặng</a>
				</li>
				<li class="one-item <?php if ($page == 'event') echo 'active' ?>" id="event">
					<a href="<?php echo MAIN_URL ?>/event">Events</a>
				</li>
				<li class="one-item <?php if ($page == 'ask') echo 'active' ?>" id="ask">
					<a href="<?php echo MAIN_URL ?>/ask">Ask</a>
				</li>
				<li class="one-item <?php if ($page == 'request') echo 'active' ?>" id="request">
					<a href="<?php echo MAIN_URL ?>/request">Yêu cầu</a>
				</li -->
				<li class="one-item <?php if ($page == 'group') echo 'active' ?>" id="group">
					<a href="<?php echo MAIN_URL ?>/group">Nhóm</a>
				</li>
				<!--<li class="one-item <?php if ($page == 'storage') echo 'active' ?>" id="storage">
					<a href="<?php echo MAIN_URL ?>/storage">Kho</a>
				</li>
				<li class="one-item hidden <?php if ($page == 'print') echo 'active' ?>" id="print">
					<a href="<?php echo MAIN_URL ?>/print">Bản in</a>
				</li>
				<li class="one-item <?php if ($page == 'search') echo 'active' ?>" id="search">
					<a href="<?php echo MAIN_URL ?>/search">Tìm kiếm</a>
				</li>
				<li class="one-item <?php if ($page == 'box') echo 'active' ?>" id="box">
					<a href="<?php echo MAIN_URL ?>/box">#bookStop</a>
				</li>-->
			</ul>

		<div class="form-search">
			<form class="search-form">
				<input name="keywords" class="search-input" placeholder="Input something..." type="text">
				<div id="search_button" class="search-button"></div>
			</form>
		</div>
	</nav>
</header>


<div id="main-content" class="page-<?php echo $page ?>">
	<menu class="menu-left col-lg-3">
		<?php include 'menu/'.$page.'.php' ?>
	</menu>
	<main class="col-lg-9">
