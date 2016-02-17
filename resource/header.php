<?php
echo '	<!doctype html>
		<html>
		<head>
		<meta charset="utf-8">
		<title>'.$title.'</title>
		</head>
		<link href="resource/style.css" rel="stylesheet">
		</head>
		<body>
		<div class="wrapper">
			<div class="header">
				<div class="divmenu">
					<a href="index.php"><div class="menu">Прочитанные книги</div></a>
					<a href="addbook.php"><div class="menu">Добавить книгу</div></a>
					<a href="addfromxml.php"><div class="menu">Добавить книгу из XML</div></a>
				</div>
			</div>
		<div class="content">
		<br>
		<div class="divtext"><b>'.$title.'</b></div><br>';