	<?php
		define (QUERY, "SELECT a.name AS 'Автор', b.name AS 'Название книги', d.rdate AS 'Дата прочтения', b.skin AS 'Обложка'
				FROM datereading d INNER JOIN
				(book b INNER JOIN author a
				ON b.id_author = a.id)
				ON d.id_book = b.id
				ORDER BY d.rdate");
				
		require_once "settings.php";
		
		try
		{
			$connection = new PDO(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);//подключение к базе данных
			$connection->query("SET NAMES 'utf8'");
		}
		catch (PDOException $p)
		{
			echo 'Не удалось подкоючиться к базе данных: ' . $p->getMessage();
			exit();
		}
		
		function printtable ($result)//вывод таблицы с описание книги
		{
			echo '<table align="center"><col width="145"><col width="255"><tr align="center"><th style="border-top-left-radius: 10px;">Автор</th><th>Название книги</th><th>Дата прочтения</th><th style="border-top-right-radius: 10px;">Обложка</th></tr>';
			foreach ($result as $row)
			{
				echo '<tr><td>'.$row['Автор'].'</td><td>'.$row['Название книги'].'</td><td align="center">'.date('d.m.Y', strtotime($row['Дата прочтения'])).'</td>';
				if ($row['Обложка'] == NULL)
				{
					echo '<td align="center">Обложка отсутствует</td></tr>';	
				}
				else
				{
					echo '<td align="center"><img height="200" border="1" src="'.$row['Обложка'].'"></td></tr>';		
				}
			}
			echo '</table><br>';
		}
		
		function insertauthorname ($authorname)//добавление в базу данных имени автора
		{
			global $connection;
			$stmt = $connection->prepare("SELECT * FROM author a WHERE a.name = ?");
			$stmt->execute(array($authorname));
			if ($stmt->rowCount() == 0)
			{
				$stmt = $connection->prepare("INSERT INTO author VALUES ('', ?)");
				$stmt->execute(array($authorname));
				return $connection->lastInsertId();
			}
			else
			{
				foreach ($stmt->fetch() as $row)
				{
					return $row["id"];
				}
			}
		}
		
		function insertbookname ($id_author, $bookname, $skin)//добавление в базу данных название книги и обложки
		{
			global $connection;
			$stmt = $connection->prepare("SELECT * FROM book b WHERE b.name = ? AND b.id_author = ?");
			$stmt->execute(array($bookname, $id_author));
			if ($stmt->rowCount() == 0)//если книги нет в базе данных, добавляем ее
			{
				$image = "NULL";//флаг названия обложки
				if (isset($skin['name']))//если обложка указана и ее формат .png или .jpg, то сохраняем ее
				{
					if ($skin['error'] == 0)
					{
						if ($skin['type'] == 'image/png' || $skin['type'] == 'image/jpeg')
						{
							move_uploaded_file($skin['tmp_name'], "skins/".$skin['name']);
							$image = "skins/".$skin['name'];
						}
						else
						{
							echo '<div class="divtext">Выберите графический файла типа .png или .jpg</div>';
							return;//выход из функции
						}
					}
				}
				if ($image == 'NULL')//если обложка не была указана, записываем в базу данных NULL
				{
					$stmt = $connection->prepare("INSERT INTO book VALUES ('', ?, ?, NULL)");
					$stmt->execute(array($id_author, $bookname));
				}
				else//иначе записываем местоположение обложки
				{
					$stmt = $connection->prepare("INSERT INTO book VALUES ('', ?, ?, ?)");
					$stmt->execute(array($id_author, $bookname, $image));
				}
				return $connection->lastInsertId();//возвращаем (запоминаем) id книги
			}
			else//иначе возвращаем данные о книге
			{
				while ($row = $stmt->fetch())
				{
					$id_book = $row['id'];
				}
				$stmt = $connection->prepare("SELECT a.name AS 'Автор', b.name AS 'Название книги', d.rdate AS 'Дата прочтения', b.skin AS 'Обложка'
					FROM datereading d INNER JOIN
					(book b INNER JOIN author a
					ON b.id_author = a.id)
					ON d.id_book = b.id
					WHERE a.id = ? AND b.id = ?
					ORDER BY d.rdate");
				$stmt->execute(array($id_author, $id_book));
				return $stmt->fetchAll();
			}
		}
		
		function inserdatereading($id_book, $datereading)//добавление в базу данных дату прочтения книги
		{
			global $connection;
			$stmt = $connection->prepare("INSERT INTO datereading VALUES ('', ?, ?)");
			$stmt->execute(array($id_book, date('Y-m-d', strtotime($datereading))));
		}
		
		function insertfromxml ($file)////добавление в базу дынных книг из xml файла
		{
			if ($file['error'] == 0)
			{
				if ($file['type'] == 'text/xml')
				{						
					$xml = simplexml_load_file($file['tmp_name']);
					$i = $j = 0;
					foreach ($xml->readbook as $readbook)
					{
						$authorname = $readbook->authorname;
						$bookname = $readbook->bookname;
						$datereading = $readbook->datereading;
						$book = insertbookname(insertauthorname($authorname), $bookname, NULL);
						if (is_string($book))//если книги нет в базе данных, добавляем ее
						{
							inserdatereading($book, $datereading);
							//запоминаем данные о новой книге в массиве $array[n]
							$array[n][$i]['Автор'] = $authorname; 
							$array[n][$i]['Название книги'] = $bookname;
							$array[n][$i]['Дата прочтения'] = $datereading;
							$array[n][$i++]['Обложка'] = NULL;
						}
						else//иначе запоминаем данные о прочитанной книге в массиве $array[o]
						{
							$array[o][$j]['Автор'] = $authorname;
							$array[o][$j]['Название книги'] = $bookname;
							$array[o][$j]['Дата прочтения'] = $datereading;
							$array[o][$j++]['Обложка'] = NULL;
						}
					}
					echo '<div class="divtext">Всего книг в  XML файле: '.($i + $j).'</div><br>';
					if (isset($array[n]))//если книги добавлялись, выводим их
					{
						echo '<div class="divtext">Добавлено книг из XML файла: '.count($array[n]).'</div>';
						printtable($array[n]);					
					}
					else//иначе выводим сообщение, что добавлено 0 кнги
					{
						echo '<div class="divtext">Добавлено книг из XML файла: '.count($array[n]).'</div><br>';
					}
					if (isset($array[o]))//если книги из xml файла уже существуют в базе, выводим их
					{
						echo '<div class="divtext">Прочитанные книги из XML файла: '.count($array[o]).'</div>';
						printtable($array[o]);
					}
					else//иначе выводим сообщение, что прочитанных кнги нет
					{
						echo '<div class="divtext">Прочитанные книги из XML файла: '.count($array[o]).'</div>';
					}
				}
				else//вывод сообщения о несоответствии типа файла
				{
					echo '<div class="divtext">Тип файла не соответствует XML!</div>';
				}
			}
		}
	?>
