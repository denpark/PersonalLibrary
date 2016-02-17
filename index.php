<?php
	require_once "resource/functions.php";
	$title = "Прочитанные книги";
	require_once "resource/header.php";
	echo '<center><a href="addbook.php">Добавить книгу</a></center><br>';
	$stmt = $connection->prepare(QUERY);
	$stmt->execute();
	if ($stmt->rowcount() == 0)
	{
		echo '<div class="divtext">Прочитанных книг нет.</div><br>';
		exit();
	}
	printtable ($stmt->fetchAll());
	echo '<center><a href="addbook.php">Добавить книгу</a></center>';
	require_once "resource/footer.php";
?>  
