<?php
	require_once "resource/functions.php";
	myheader("Прочитанные книги");
	$result = queryerror(query);
	if ($result->rowcount() == 0)
	{
		echo '<div class="divtext">Прочитанных книг нет.</div><br>';
		exit();
	}
	printtable ($result);
	myfooter();		
?>  
