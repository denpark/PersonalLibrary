<?php
	require_once "resource/functions.php";
	$title = "Добавить книгу из XML";
	require_once "resource/header.php";
	echo '<center><a href="addbook.php">Добавить книгу</a></center><br>';
	if (isset($_FILES['xmlfile']['name']))
	{
		insertfromxml ($_FILES['xmlfile']);
	}
	echo '<div class="divform"><br>
	<form method="post" action="addfromxml.php" enctype=multipart/form-data>
	<input type="file" name="xmlfile" required="required"><br>
	<input type="submit" value="Добавить">
	</div>';
	require_once "resource/footer.php";
?>
