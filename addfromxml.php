<?php
	require_once "resource/functions.php";
	$title = "Добавить книгу из XML";
	$href_file = "addbook.php";
	$href_name = "Добавить книгу";
	require_once "resource/header.html";
	if (isset($_FILES['xmlfile']['name']))
	{
		insertfromxml ($_FILES['xmlfile']);
	}
?>
	<div class="divform"><br>
	<form method="post" action="addfromxml.php" enctype=multipart/form-data>
	<input type="file" name="xmlfile" required="required"><br>
	<input type="submit" value="Добавить">
	</div>
<?php
	require_once "resource/footer.html";
?>
