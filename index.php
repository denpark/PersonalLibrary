<?php
	require_once "resource/functions.php";
	$title = "Прочитанные книги";
	$href_file = "addbook.php";
	$href_name = "Добавить книгу";
	require_once "resource/header.html";
	$stmt = $connection->prepare(QUERY);
	$stmt->execute();
	if ($stmt->rowcount() == 0)
	{
		echo '<div class="divtext">Прочитанных книг нет.</div><br>';
		exit();
	}
	printtable ($stmt->fetchAll());
	require_once "resource/footer.html";
?>  
