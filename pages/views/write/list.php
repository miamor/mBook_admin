<?php
if (!$do && !$v && !$temp) include 'pages/views/_temp/header.php';

include 'objects/genre.php';
$genre = new Genre();
$genre->readAll();
$genList = $genre->all_list;

include 'objects/author.php';
$au = new Author();
$au->readAll();
$auList = $au->all_list;

//$config->addJS('plugins', 'DataTables/datatables.min.js');
$config->addJS('dist', 'write/list.js');

include 'pages/views/_temp/'.$page.'/list.php';
