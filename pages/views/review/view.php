<?php
	if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

			$config->addJS('plugins', 'DataTables/datatables.min.js');
			$config->addJS('dist', 'ratings.min.js');
			$config->addJS('dist', $page.'/view.js');
			include 'pages/views/_temp/'.$page.'/view.php';
