<?php

		if ($bChap['id']) {
			$page_title = 'Sửa chương '.$bChap['title'];
				
			if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

			include 'pages/views/_temp/write/mode.'.$mode.'.php';
		}
