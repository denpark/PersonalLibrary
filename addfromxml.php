<?php
	require_once "resource/functions.php";
	myheader("Добавить книгу из XML");
	if (isset($_FILES['xmlfile']['name']))
	{
		insertfromxml ($_FILES['xmlfile']);
	}
	echo '<div class="divform"><br>
	<form method="post" action="addfromxml.php" enctype=multipart/form-data>
	<input type="file" name="xmlfile" required="required"><br>
	<input type="submit" value="Добавить">
	</div>';
	myfooter();	
?>
