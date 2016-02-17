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
		
		function fchar($p)//обезвреживание введенных данных
		{
			return nl2br(htmlspecialchars(trim($p), ENT_QUOTES), false);
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
		
		function queryerror ($query)//проверка выполнения запроса
		{
			global $connection;
			$result = $connection->query($query);
			if (!$result)
			{	
				echo 'Не удалось выполнить запрос: ';
				print_r($connection->errorInfo());
				exit();	
			}
			else
			{
				return $result;
			}
		}
		
		function insertauthorname ($authorname)//добавление в базу данных имени автора
		{
			global $connection;
			$authorname = fchar($authorname);
			$query = "SELECT * FROM author a WHERE a.name='".$authorname."'";//запрос на существование автора
			$result = queryerror($query);
			if ($result->rowCount() == 0)//если автора нет в базе данных, добавляем его
			{
				$query = "INSERT INTO author VALUES ('', '".$authorname."')";
				queryerror($query);				
				return $connection->lastInsertId();//возвращаем (запоминаем) id	автора		
			}
			else//иначе возвращаем (запоминаем) id автора
			{
				foreach ($result as $row)
				{
					return $row['id'];	
				}
			}
		}
		
		function insertbookname ($id_author, $bookname, $skin)//добавление в базу данных название книги и обложки
		{
			global $connection;
			$bookname = fchar($bookname);
			if (!is_null($skin))
			{
				$skin['name'] = fchar($skin['name']);
				$skin['tmp_name'] = fchar($skin['tmp_name']);
				$skin['error'] = fchar($skin['error']);
				$skin['type'] = fchar($skin['type']);
			}
			$query = "SELECT * FROM book b WHERE b.name='".$bookname."' AND b.id_author='".$id_author."'";//запрос на существование книги
			$result = queryerror($query);
			if ($result->rowCount() == 0)//если книги нет в базе данных, добавляем ее
			{
				global $connection;
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
					$query = "INSERT INTO book VALUES ('', '".$id_author."', '".$bookname."', $image)";	
				}
				else//иначе записываем местоположение обложки 
				{
					$query = "INSERT INTO book VALUES ('', '".$id_author."', '".$bookname."', '$image')";
				}				
				queryerror($query);
				return $connection->lastInsertId();//возвращаем (запоминаем) id книги			
			}
			else//иначе возвращаем данные о книге
			{
				foreach ($result as $row)
				{					
					$id_book = $row['id'];	
				}
				$query = "	SELECT a.name AS 'Автор', b.name AS 'Название книги', d.rdate AS 'Дата прочтения', b.skin AS 'Обложка'
					FROM datereading d INNER JOIN
					(book b INNER JOIN author a
					ON b.id_author = a.id)
					ON d.id_book = b.id
					WHERE a.id = ".$id_author." AND b.id = ".$id_book."
					ORDER BY d.rdate";
				return queryerror($query);
			}				
		}
		
		function inserdatereading($id_book, $datereading)//добавление в базу данных дату прочтения книги
		{
			$datereading = fchar($datereading);
			$query = "INSERT INTO datereading VALUES ('', '".$id_book."', '".date('Y-m-d', strtotime($datereading))."')";
			queryerror($query);			
		}
		
		function insertfromxml ($file)////добавление в базу дынных книг из xml файла
		{
			$file['name'] = fchar($file['name']);
			$file['error'] = fchar($file['error']);
			$file['type'] = fchar($file['type']);
			$file['tmp_name'] = fchar($file['tmp_name']);
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
		
		function myheader ($p)
		{
			echo '	<!doctype html>
					<html>
					<head>
					<meta charset="utf-8">
					<title>'.$p.'</title>
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
					<div class="divtext"><b>'.$p.'</b></div><br>';
		}
		
		function myfooter ()
		{
			echo '	</div>
					<div class="content" style="height: 40px; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px;"></div>
					</div>
					</body>
					</html>';	
		}
	?>
