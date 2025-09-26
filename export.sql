SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `auth` (
  `id` int(10) NOT NULL,
  `realname` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `date` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categories` (
  `id` int(10) NOT NULL,
  `section_id` int(10) NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0',
  `user_id_create` int(10) NOT NULL DEFAULT '0',
  `user_id_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `section_id`, `title`, `text`, `date_create`, `date_update`, `user_id_create`, `user_id_update`) VALUES
(1, 1, 'Подкатегория1', 'Тестовая подкатегория #1', 1596008268, 1596262968, 1, 1),
(2, 1, 'Подкатегория2', 'Тестовая подкатегория #2', 1596008268, 1596008268, 1, 1),
(3, 1, 'Подкатегория3', 'Тестовая подкатегория #3', 1596008268, 1596264853, 0, 1),
(4, 1, 'Подкатегория4', 'Тестовая подкатегория #4', 1596008268, 1596008268, 1, 1),
(5, 1, 'Подкатегория5', 'test', 1596261775, 1596264893, 1, 1),
(6, 1, 'Подкатегория6', 'Подкатегория6', 1596264959, 1596264959, 1, 1),
(7, 2, 'Подкатегория7', 'Подкатегория7', 1596264989, 1596264989, 1, 1),
(8, 2, 'Подкатегория8', 'Подкатегория8', 1596264999, 1596264999, 1, 1),
(9, 2, 'Подкатегория9', 'Подкатегория9', 1596265009, 1596265009, 1, 1);

CREATE TABLE `items` (
  `id` int(10) NOT NULL,
  `title` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `text` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `section_id` int(10) NOT NULL DEFAULT '0',
  `category_id` int(10) NOT NULL DEFAULT '0',
  `server_id` int(10) NOT NULL DEFAULT '0',
  `command` text COLLATE utf8mb4_general_ci,
  `surcharge` tinyint(1) NOT NULL DEFAULT '0',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0',
  `user_id_create` int(10) NOT NULL DEFAULT '0',
  `user_id_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items` (`id`, `title`, `name`, `text`, `image`, `price`, `section_id`, `category_id`, `server_id`, `command`, `surcharge`, `date_create`, `date_update`, `user_id_create`, `user_id_update`) VALUES
(1, 'Тестовый товар #1', 'Testoviy-tovar--1', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', '', '100.00', 1, 4, 1, 'asdfsad', 0, 1596189855, 1596190360, 1, 1),
(2, 'Тестовый товар #2', 'Testoviy-tovar-2', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', '', '100.00', 1, 2, 1, 'asdfsad', 0, 1596189855, 1596262986, 1, 1),
(3, 'Тестовый товар #3', 'Testoviy-tovar-3', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', '', '140.00', 1, 4, 1, 'asdfsad', 0, 1596189855, 1596190360, 1, 1),
(4, 'Тестовый товар #4', 'Testoviy-tovar-4', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', '', '13.00', 1, 4, 1, 'asdfsad', 0, 1596189855, 1596190360, 1, 1),
(5, 'Тестовый товар #5', 'Testoviy-tovar-5', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', '', '34.00', 1, 4, 1, 'asdfsad', 0, 1596189855, 1596190360, 1, 1),
(6, 'Тестовый товар #6', 'test6', 'Тестовый товар #6', '', '1000.00', 4, 0, 2, 'dsfsd', 0, 1596261897, 1596261897, 1, 1);

CREATE TABLE `payments` (
  `id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL DEFAULT '0',
  `sum` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `player` varchar(32) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL DEFAULT '1.00',
  `response` text,
  `data` text,
  `promo_id` int(10) NOT NULL DEFAULT '0',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `payments` (`id`, `item_id`, `sum`, `status`, `done`, `player`, `amount`, `response`, `data`, `promo_id`, `date_create`, `date_update`) VALUES
(3, 2, '50.00', 1, 0, 'admin', '1.00', NULL, NULL, 0, 1596290564, 1596290564),
(4, 0, '23.00', 1, 0, 'topadmin', '1.00', NULL, NULL, 0, 1596290564, 1596290564),
(5, 0, '1.00', 1, 0, 'test', '1.00', '', '', 0, 1596301785, 1596301785),
(6, 0, '90.00', 1, 0, 'test', '100.00', '', '', 2, 1596359002, 1596359002),
(7, 0, '90.00', 1, 0, 'test', '100.00', '', '', 2, 1596359129, 1596359129),
(8, 6, '810.00', 1, 0, 'admin', '1.00', '', '', 2, 1596363452, 1596363452),
(9, 6, '810.00', 1, 0, 'admin', '1.00', '', '', 2, 1596363452, 1596363452),
(10, 6, '810.00', 1, 0, 'admin', '1.00', '', '', 2, 1596363452, 1596363452),
(11, 6, '810.00', 1, 0, 'admin', '1.00', '', '', 2, 1596363452, 1596363452),
(12, 6, '810.00', 1, 0, 'admin', '1.00', '', '', 2, 1596363452, 1596363452);

CREATE TABLE `promo` (
  `id` int(10) NOT NULL,
  `code` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0',
  `user_id_create` int(10) NOT NULL DEFAULT '0',
  `user_id_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `promo` (`id`, `code`, `discount`, `date_create`, `date_update`, `user_id_create`, `user_id_update`) VALUES
(1, 'test111', '10.00', 1596195731, 1596195850, 1, 1),
(2, 'A2BA1732DE', '10.00', 1596195882, 1596195882, 1, 1);

CREATE TABLE `sections` (
  `id` int(10) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `text` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0',
  `user_id_create` int(10) NOT NULL DEFAULT '0',
  `user_id_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sections` (`id`, `title`, `text`, `date_create`, `date_update`, `user_id_create`, `user_id_update`) VALUES
(1, 'Категория1', 'Тестовая категория #1', 1596008105, 1596008105, 1, 1),
(2, 'Категория2', 'Тестовая категория #2', 1596008105, 1596008105, 1, 1),
(3, 'Категория3', 'Тестовая категория #3', 1596008105, 1596008105, 1, 1),
(4, 'Категория4', 'Тестовая категория #4', 1596008105, 1596183906, 1, 1),
(5, 'Категория5', 'Тестовая категория #5', 1596008105, 1596008105, 1, 1);

CREATE TABLE `servers` (
  `id` int(10) NOT NULL,
  `title` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `text` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `rcon_host` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '127.0.0.1',
  `rcon_port` int(10) NOT NULL DEFAULT '25575',
  `rcon_password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0',
  `user_id_create` int(10) NOT NULL DEFAULT '0',
  `user_id_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `servers` (`id`, `title`, `text`, `rcon_host`, `rcon_port`, `rcon_password`, `date_create`, `date_update`, `user_id_create`, `user_id_update`) VALUES
(1, 'Выживание', 'RCON Сервер выживания', '127.0.0.1', 25575, '123456', 1596012041, 1596012041, 1, 1),
(2, 'MiniGames', 'Мини-игры', '127.0.0.1', 25576, '', 1596153037, 1596153037, 1, 1);

CREATE TABLE `statics` (
  `id` int(10) NOT NULL,
  `title` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8mb4_general_ci,
  `user_id_create` int(10) NOT NULL DEFAULT '0',
  `user_id_update` int(10) NOT NULL DEFAULT '0',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `statics` (`id`, `title`, `name`, `content`, `user_id_create`, `user_id_update`, `date_create`, `date_update`) VALUES
(1, 'Начать играть', 'start', '<!doctype html>\r\n<html lang=\"ru\">\r\n<head>\r\n	 <title>Начать играть | {{ __CONFIG__.meta.sitename }}</title>\r\n\r\n	 {{ include(\'header.tpl\') }}\r\n</head>\r\n<body>\r\n\r\n{% set nav = \'start\' %}\r\n{{ include(\'navbar.tpl\') }}\r\n\r\n<div class=\"container\">\r\n	 <div class=\"window\">\r\n		  Начать играть\r\n	 </div>\r\n</div>\r\n\r\n{{ include(\'footer.tpl\') }}\r\n\r\n</body>\r\n</html>', 1, 1, 1596196371, 1596202501),
(2, 'Правила', 'rules', '<!doctype html>\r\n<html lang=\"ru\">\r\n<head>\r\n	 <title>Правила | {{ __CONFIG__.meta.sitename }}</title>\r\n\r\n	 {{ include(\'header.tpl\') }}\r\n</head>\r\n<body>\r\n\r\n{% set nav = \'rules\' %}\r\n{{ include(\'navbar.tpl\') }}\r\n\r\n<div class=\"container\">\r\n	 <div class=\"window\">\r\n		  Правила\r\n	 </div>\r\n</div>\r\n\r\n{{ include(\'footer.tpl\') }}\r\n\r\n</body>\r\n</html>', 1, 1, 1596202480, 1596202513),
(3, 'Политика кофидециальности', 'privacy', '<!doctype html>\r\n<html lang=\"ru\">\r\n<head>\r\n	 <title>Политика кофидециальности | {{ __CONFIG__.meta.sitename }}</title>\r\n\r\n	 {{ include(\'header.tpl\') }}\r\n</head>\r\n<body>\r\n\r\n{% set nav = \'privacy\' %}\r\n{{ include(\'navbar.tpl\') }}\r\n\r\n<div class=\"container\">\r\n	 <div class=\"window\">\r\n		  Политика кофидециальности\r\n	 </div>\r\n</div>\r\n\r\n{{ include(\'footer.tpl\') }}\r\n\r\n</body>\r\n</html>', 1, 1, 1596202562, 1596202562),
(4, 'Договор офферты', 'offer', '<!doctype html>\r\n<html lang=\"ru\">\r\n<head>\r\n	 <title>Договор офферты | {{ __CONFIG__.meta.sitename }}</title>\r\n\r\n	 {{ include(\'header.tpl\') }}\r\n</head>\r\n<body>\r\n\r\n{% set nav = \'servers\' %}\r\n{{ include(\'navbar.tpl\') }}\r\n\r\n<div class=\"container\">\r\n	 <div class=\"window\">\r\n		  Договор офферты\r\n	 </div>\r\n</div>\r\n\r\n{{ include(\'footer.tpl\') }}\r\n\r\n</body>\r\n</html>', 1, 1, 1596202590, 1596202590);

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `login` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `email` varchar(128) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `password` varchar(128) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_update` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `login`, `email`, `password`, `date_create`, `date_update`) VALUES
(1, 'admin', 'admin@ucave.ru', '$2y$12$33nSUkRNPXUwQh193m6KfuWU.yut7AKtZHYkTWwjYF6zj5uQ2mz6a', 1596007944, 1596372730);

CREATE TABLE `users_auth` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '127.0.0.1',
  `token` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `date_create` int(10) NOT NULL DEFAULT '0',
  `date_expire` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

CREATE TABLE `users_sum` (
  `player` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `sum` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `statics`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users_auth`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `auth`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `items`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `payments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `promo`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `sections`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `servers`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `statics`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `users_auth`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
