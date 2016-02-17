<?php
	require_once "resource/functions.php";
	myheader("Прочитанные книги");
	echo '<center><a href="addbook.php">Добавить книгу</a></center><br>';
	$result = queryerror(QUERY);
	if ($result->rowcount() == 0)
	{
		echo '<div class="divtext">Прочитанных книг нет.</div><br>';
		exit();
	}
	printtable ($result);
	echo '<center><a href="addbook.php">Добавить книгу</a></center>';
	myfooter();		
?>  
