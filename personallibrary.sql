-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 17 2016 г., 16:23
-- Версия сервера: 5.5.47-0ubuntu0.14.04.1
-- Версия PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `personallibrary`
--

-- --------------------------------------------------------

--
-- Структура таблицы `author`
--

CREATE TABLE IF NOT EXISTS `author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `author`
--

INSERT INTO `author` (`id`, `name`) VALUES
(1, 'Александр Жадаев'),
(2, 'Иэн Гриффитс'),
(3, 'Робин Никсон'),
(5, 'Дэвид Скляр'),
(6, 'Владимир Дронов'),
(8, 'Кузнецов');

-- --------------------------------------------------------

--
-- Структура таблицы `book`
--

CREATE TABLE IF NOT EXISTS `book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_author` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `skin` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_author` (`id_author`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `book`
--

INSERT INTO `book` (`id`, `id_author`, `name`, `skin`) VALUES
(1, 1, 'PHP для начинающих', 'skins/08899809.cover.jpg'),
(2, 2, 'Программирование на C# 5.0', NULL),
(3, 3, 'PHP, MySQL, JavaScript, CSS и HTML5', NULL),
(5, 5, 'PHP и MySQL', 'skins/11053241.cover.jpg'),
(6, 6, 'HTML, JavaScript, PHP и MySQL', NULL),
(7, 1, 'HTML, JavaScript, PHP и MySQL', 'skins/08899809.cover.jpg'),
(8, 6, 'PHP для начинающих', NULL),
(10, 8, 'English 6', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `datereading`
--

CREATE TABLE IF NOT EXISTS `datereading` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_book` int(11) DEFAULT NULL,
  `rdate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_book` (`id_book`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `datereading`
--

INSERT INTO `datereading` (`id`, `id_book`, `rdate`) VALUES
(1, 1, '2015-02-04'),
(2, 2, '2016-02-02'),
(3, 3, '2015-11-21'),
(5, 5, '2016-01-13'),
(6, 6, '2015-03-21'),
(7, 7, '2015-06-03'),
(8, 8, '2014-06-20'),
(10, 10, '2007-05-20');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_2` FOREIGN KEY (`id_author`) REFERENCES `author` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `datereading`
--
ALTER TABLE `datereading`
  ADD CONSTRAINT `datereading_ibfk_2` FOREIGN KEY (`id_book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
