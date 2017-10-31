<?php
if ($bView['type'] == 1) $page_title = 'Thêm bài viết mới - '.$title;
else $page_title = 'Thêm chương mới - '.$title;
if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

include 'pages/views/_temp/write/mode.'.$mode.'.php';
