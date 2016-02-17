	<?php
		require_once "resource/functions.php";
		myheader("Добавить книгу");
		if (isset($_POST['authorname'], $_POST['bookname'], $_POST['datereading']))
		{		
			if (strtotime($_POST['datereading']) != false)
			{	
				$book = insertbookname(insertauthorname($_POST['authorname']), $_POST['bookname'], $_FILES['skin']);
				if (is_string($book))//если функция insertbookname вернула строку, значит книги в базе данных нет
				{
				inserdatereading($book, $_POST['datereading']);//добавляем книгу
				echo '<div class="divtext">Книга успешно добавлена.</div>';
				}
				elseif (!is_null($book))//если функция insertbookname вернула не NULL, знчит книга существует в базе данных
				{
					echo '<div class="divtext">Эту книгу Вы уже прочитали:</div><br>';
					printtable($book);//вывод прочитанной книги
				}
			}
			else
			{
				echo '<div class="divtext">Введанная дата некоректна!</div><br>';
			}			
		}
	?>
    <div class="divform">
    <form method="post" action="addbook.php" enctype="multipart/form-data"><br>
    <input type="text" name="authorname" maxlength="50" placeholder="Имя автора" required><br>
    <input type="text" name="bookname" maxlength="50" placeholder="Название книги" required><br>
    <input type="file" name="skin" title="Файл обложки"><br>
    <input type="date" name="datereading" placeholder="Дата прочтения" required><br>
    <input type="submit" value="Добавить">
    </form>
    </div>
	<?php 
		myfooter();
	?>
