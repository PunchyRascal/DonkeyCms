-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heading` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `art` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `discussion` smallint(6) DEFAULT NULL,
  `category` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `enable_discussion` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `art_id` (`id`),
  KEY `category` (`category`),
  KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `article` (`id`, `heading`, `teaser`, `art`, `date`, `discussion`, `category`, `author`, `views`, `enable_discussion`) VALUES
(1,	'Lorem Ipsum',	'Dolor sit amet.',	'',	'2017-04-14 16:01:02',	NULL,	'obyvatele',	'',	0,	0);

CREATE TABLE `art_image` (
  `home_art` mediumint(8) unsigned NOT NULL,
  `art_img_count` mediumint(9) unsigned NOT NULL,
  `extension` varchar(10) CHARACTER SET latin1 NOT NULL,
  KEY `home_art` (`home_art`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `art_rate` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `home` mediumint(9) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `user` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `home` (`home`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `home` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `date_r` varchar(25) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `user` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `home` (`home`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `click` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `hits` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `home` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `home` (`home`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `e_admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `last_login_date` datetime DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `e_admin_user` (`id`, `login`, `password`, `active`, `last_login_date`, `creation_date`) VALUES
(1,	'donkey',	'$2y$10$Z5U24md3J7DuEdzKOqAfDuEsSksxBak7HGS8OFTUWVDNsQMbiHRT2',	1,	NULL,	'2017-04-17 13:48:31');

CREATE TABLE `e_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `active` int(11) NOT NULL,
  `parent_cat` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `children` text COLLATE utf8_czech_ci,
  `image_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_cat` (`parent_cat`),
  KEY `sequence` (`sequence`),
  KEY `name` (`name`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `e_dates` (
  `date_record` date NOT NULL,
  PRIMARY KEY (`date_record`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `e_dates` (`date_record`) VALUES
('1999-09-15'),
('1999-10-15'),
('1999-11-15'),
('1999-12-15'),
('2000-01-15'),
('2000-02-15'),
('2000-03-15'),
('2000-04-15'),
('2000-05-15'),
('2000-06-15'),
('2000-07-15'),
('2000-08-15'),
('2000-09-15'),
('2000-10-15'),
('2000-11-15'),
('2000-12-15'),
('2001-01-15'),
('2001-02-15'),
('2001-03-15'),
('2001-04-15'),
('2001-05-15'),
('2001-06-15'),
('2001-07-15'),
('2001-08-15'),
('2001-09-15'),
('2001-10-15'),
('2001-11-15'),
('2001-12-15'),
('2002-01-15'),
('2002-02-15'),
('2002-03-15'),
('2002-04-15'),
('2002-05-15'),
('2002-06-15'),
('2002-07-15'),
('2002-08-15'),
('2002-09-15'),
('2002-10-15'),
('2002-11-15'),
('2002-12-15'),
('2003-01-15'),
('2003-02-15'),
('2003-03-15'),
('2003-04-15'),
('2003-05-15'),
('2003-06-15'),
('2003-07-15'),
('2003-08-15'),
('2003-09-15'),
('2003-10-15'),
('2003-11-15'),
('2003-12-15'),
('2004-01-15'),
('2004-02-15'),
('2004-03-15'),
('2004-04-15'),
('2004-05-15'),
('2004-06-15'),
('2004-07-15'),
('2004-08-15'),
('2004-09-15'),
('2004-10-15'),
('2004-11-15'),
('2004-12-15'),
('2005-01-15'),
('2005-02-15'),
('2005-03-15'),
('2005-04-15'),
('2005-05-15'),
('2005-06-15'),
('2005-07-15'),
('2005-08-15'),
('2005-09-15'),
('2005-10-15'),
('2005-11-15'),
('2005-12-15'),
('2006-01-15'),
('2006-02-15'),
('2006-03-15'),
('2006-04-15'),
('2006-05-15'),
('2006-06-15'),
('2006-07-15'),
('2006-08-15'),
('2006-09-15'),
('2006-10-15'),
('2006-11-15'),
('2006-12-15'),
('2007-01-15'),
('2007-02-15'),
('2007-03-15'),
('2007-04-15'),
('2007-05-15'),
('2007-06-15'),
('2007-07-15'),
('2007-08-15'),
('2007-09-15'),
('2007-10-15'),
('2007-11-15'),
('2007-12-15'),
('2008-01-15'),
('2008-02-15'),
('2008-03-15'),
('2008-04-15'),
('2008-05-15'),
('2008-06-15'),
('2008-07-15'),
('2008-08-15'),
('2008-09-15'),
('2008-10-15'),
('2008-11-15'),
('2008-12-15'),
('2009-01-15'),
('2009-02-15'),
('2009-03-15'),
('2009-04-15'),
('2009-05-15'),
('2009-06-15'),
('2009-07-15'),
('2009-08-15'),
('2009-09-15'),
('2009-10-15'),
('2009-11-15'),
('2009-12-15'),
('2010-01-15'),
('2010-02-15'),
('2010-03-15'),
('2010-04-15'),
('2010-05-15'),
('2010-06-15'),
('2010-07-15'),
('2010-08-15'),
('2010-09-15'),
('2010-10-15'),
('2010-11-15'),
('2010-12-15'),
('2011-01-15'),
('2011-02-15'),
('2011-03-15'),
('2011-04-15'),
('2011-05-15'),
('2011-06-15'),
('2011-07-15'),
('2011-08-15'),
('2011-09-15'),
('2011-10-15'),
('2011-11-15'),
('2011-12-15'),
('2012-01-15'),
('2012-02-15'),
('2012-03-15'),
('2012-04-15'),
('2012-05-15'),
('2012-06-15'),
('2012-07-15'),
('2012-08-15'),
('2012-09-15'),
('2012-10-15'),
('2012-11-15'),
('2012-12-15'),
('2013-01-15'),
('2013-02-15'),
('2013-03-15'),
('2013-04-15'),
('2013-05-15'),
('2013-06-15'),
('2013-07-15'),
('2013-08-15'),
('2013-09-15'),
('2013-10-15'),
('2013-11-15'),
('2013-12-15'),
('2014-01-15'),
('2014-02-15'),
('2014-03-15'),
('2014-04-15'),
('2014-05-15'),
('2014-06-15'),
('2014-07-15'),
('2014-08-15'),
('2014-09-15'),
('2014-10-15'),
('2014-11-15'),
('2014-12-15'),
('2015-01-15'),
('2015-02-15'),
('2015-03-15'),
('2015-04-15'),
('2015-05-15'),
('2015-06-15'),
('2015-07-15'),
('2015-08-15'),
('2015-09-15'),
('2015-10-15'),
('2015-11-15'),
('2015-12-15'),
('2016-01-15'),
('2016-02-15'),
('2016-03-15'),
('2016-04-15'),
('2016-05-15'),
('2016-06-15'),
('2016-07-15'),
('2016-08-15'),
('2016-09-15'),
('2016-10-15'),
('2016-11-15'),
('2016-12-15'),
('2017-01-15'),
('2017-02-15'),
('2017-03-15'),
('2017-04-15'),
('2017-05-15'),
('2017-06-15'),
('2017-07-15'),
('2017-08-15'),
('2017-09-15'),
('2017-10-15'),
('2017-11-15'),
('2017-12-15'),
('2018-01-15'),
('2018-02-15'),
('2018-03-15'),
('2018-04-15'),
('2018-05-15'),
('2018-06-15'),
('2018-07-15'),
('2018-08-15'),
('2018-09-15'),
('2018-10-15'),
('2018-11-15'),
('2018-12-15'),
('2019-01-15'),
('2019-02-15'),
('2019-03-15'),
('2019-04-15'),
('2019-05-15'),
('2019-06-15'),
('2019-07-15'),
('2019-08-15'),
('2019-09-15'),
('2019-10-15'),
('2019-11-15'),
('2019-12-15'),
('2020-01-15'),
('2020-02-15'),
('2020-03-15'),
('2020-04-15'),
('2020-05-15'),
('2020-06-15'),
('2020-07-15'),
('2020-08-15'),
('2020-09-15'),
('2020-10-15'),
('2020-11-15'),
('2020-12-15'),
('2021-01-15'),
('2021-02-15'),
('2021-03-15'),
('2021-04-15'),
('2021-05-15'),
('2021-06-15'),
('2021-07-15'),
('2021-08-15'),
('2021-09-15'),
('2021-10-15'),
('2021-11-15'),
('2021-12-15'),
('2022-01-15'),
('2022-02-15'),
('2022-03-15'),
('2022-04-15'),
('2022-05-15'),
('2022-06-15'),
('2022-07-15'),
('2022-08-15'),
('2022-09-15'),
('2022-10-15'),
('2022-11-15'),
('2022-12-15'),
('2023-01-15'),
('2023-02-15'),
('2023-03-15'),
('2023-04-15'),
('2023-05-15'),
('2023-06-15'),
('2023-07-15'),
('2023-08-15'),
('2023-09-15'),
('2023-10-15'),
('2023-11-15'),
('2023-12-15'),
('2024-01-15'),
('2024-02-15'),
('2024-03-15'),
('2024-04-15'),
('2024-05-15'),
('2024-06-15'),
('2024-07-15'),
('2024-08-15'),
('2024-09-15'),
('2024-10-15'),
('2024-11-15'),
('2024-12-15'),
('2025-01-15'),
('2025-02-15'),
('2025-03-15'),
('2025-04-15'),
('2025-05-15'),
('2025-06-15'),
('2025-07-15'),
('2025-08-15'),
('2025-09-15'),
('2025-10-15'),
('2025-11-15'),
('2025-12-15'),
('2026-01-15'),
('2026-02-15'),
('2026-03-15'),
('2026-04-15'),
('2026-05-15'),
('2026-06-15'),
('2026-07-15'),
('2026-08-15'),
('2026-09-15'),
('2026-10-15'),
('2026-11-15'),
('2026-12-15'),
('2027-01-15'),
('2027-02-15'),
('2027-03-15'),
('2027-04-15'),
('2027-05-15'),
('2027-06-15'),
('2027-07-15'),
('2027-08-15'),
('2027-09-15'),
('2027-10-15'),
('2027-11-15'),
('2027-12-15'),
('2028-01-15'),
('2028-02-15'),
('2028-03-15'),
('2028-04-15'),
('2028-05-15'),
('2028-06-15'),
('2028-07-15'),
('2028-08-15'),
('2028-09-15'),
('2028-10-15'),
('2028-11-15'),
('2028-12-15'),
('2029-01-15'),
('2029-02-15'),
('2029-03-15'),
('2029-04-15'),
('2029-05-15'),
('2029-06-15'),
('2029-07-15'),
('2029-08-15'),
('2029-09-15'),
('2029-10-15'),
('2029-11-15'),
('2029-12-15'),
('2030-01-15'),
('2030-02-15'),
('2030-03-15'),
('2030-04-15'),
('2030-05-15'),
('2030-06-15'),
('2030-07-15'),
('2030-08-15'),
('2030-09-15'),
('2030-10-15'),
('2030-11-15'),
('2030-12-15'),
('2031-01-15'),
('2031-02-15'),
('2031-03-15'),
('2031-04-15'),
('2031-05-15'),
('2031-06-15'),
('2031-07-15'),
('2031-08-15'),
('2031-09-15'),
('2031-10-15'),
('2031-11-15'),
('2031-12-15'),
('2032-01-15'),
('2032-02-15'),
('2032-03-15'),
('2032-04-15'),
('2032-05-15'),
('2032-06-15'),
('2032-07-15'),
('2032-08-15'),
('2032-09-15'),
('2032-10-15'),
('2032-11-15'),
('2032-12-15');

CREATE TABLE `e_delivery_method` (
  `id` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `price` int(11) NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `e_delivery_method` (`id`, `name`, `price`, `priority`) VALUES
('cz_ppl',	'PPL po ČR',	150,	10),
('cz_post',	'Česká pošta po ČR',	110,	20),
('personal',	'osobní odběr',	0,	30),
('zasilkovna',	'Zásilkovna',	55,	40);

CREATE TABLE `e_eet_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `fik` varchar(255) DEFAULT NULL,
  `verification_only` tinyint(4) NOT NULL,
  `sent_ok` tinyint(4) NOT NULL DEFAULT '0',
  `bkp` varchar(255) DEFAULT NULL,
  `pkp` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `e_eet_sale_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `e_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `desc` text COLLATE utf8_czech_ci NOT NULL,
  `home` int(10) DEFAULT NULL,
  `stock` int(10) unsigned NOT NULL,
  `price` mediumint(8) unsigned NOT NULL,
  `make` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `discount` int(4) unsigned NOT NULL DEFAULT '0',
  `variant` varchar(150) COLLATE utf8_czech_ci DEFAULT NULL,
  `availability` text COLLATE utf8_czech_ci,
  `ean` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `availability_days` smallint(6) NOT NULL DEFAULT '-1',
  `featured` tinyint(4) NOT NULL DEFAULT '0',
  `image_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `import_resolved` int(11) DEFAULT NULL,
  `external_id` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `origin` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `import_status` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `auto_update` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `origin_external_id` (`origin`,`external_id`),
  KEY `home` (`home`),
  KEY `stock` (`stock`),
  KEY `make` (`make`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `e_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `public_id` int(11) DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `street` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `town` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `note` text COLLATE utf8_czech_ci,
  `order` text COLLATE utf8_czech_ci,
  `date` datetime NOT NULL,
  `status` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `status_note` text COLLATE utf8_czech_ci,
  `delivery` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `payment` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `zasilkovna_branch` int(11) DEFAULT NULL,
  `zasilkovna_id` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `status_change_date` datetime DEFAULT NULL,
  `price_items` int(11) DEFAULT NULL,
  `price_delivery` int(11) DEFAULT NULL,
  `price_payment` int(11) DEFAULT NULL,
  `price_total` int(11) DEFAULT NULL,
  `vat_rate` int(11) DEFAULT NULL,
  `submission_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_id` (`public_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DELIMITER ;;

CREATE TRIGGER `e_order_bi` BEFORE INSERT ON `e_order` FOR EACH ROW
SET NEW.price_total = IFNULL(NEW.price_items, 0) + IFNULL(NEW.price_delivery, 0) + IFNULL(NEW.price_payment, 0);;

CREATE TRIGGER `e_order_bu` BEFORE UPDATE ON `e_order` FOR EACH ROW
BEGIN

SET NEW.price_total = IFNULL(NEW.price_items, 0) + IFNULL(NEW.price_delivery, 0) + IFNULL(NEW.price_payment, 0);

IF NEW.status != OLD.status THEN
    SET NEW.status_change_date = NOW();
END IF;

IF NEW.status = 1 AND OLD.status = 7 THEN
    SET NEW.submission_date = NOW();
    INSERT INTO e_order_public_id VALUE();
    SET NEW.public_id = LAST_INSERT_ID();
END IF;


END;;

DELIMITER ;

CREATE TABLE `e_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `variant` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id_product_id_variant` (`order_id`,`product_id`,`variant`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `e_order_public_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `e_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `active` int(11) NOT NULL,
  `modified` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `alert_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `e_page` (`id`, `name`, `text`, `active`, `modified`, `url`, `alert_date`) VALUES
(2,	'Foo page name',	'<p>Lorem Ipsum</p>',	1,	'2017-04-17 13:53:15',	'chci-pomahat',	NULL),
(9,	'Foo page name',	'<p>Lorem Ipsum</p>',	1,	'2017-04-17 13:53:15',	'kontakt',	NULL),
(14,	'Foo page name',	'<p>Lorem Ipsum</p>',	1,	'2017-04-17 13:53:15',	'kdo-jsme',	NULL),
(11,	'Foo page name',	'<p>Lorem Ipsum</p>',	1,	'2017-04-17 13:53:15',	'obchodni-podminky',	NULL),
(12,	'Foo page name',	'<p>Lorem Ipsum</p>',	1,	'2017-04-17 13:53:15',	'reklamacni-rad',	NULL),
(15,	'Foo page name',	'<p>Lorem Ipsum</p>',	1,	'2017-04-17 13:53:15',	'homepage',	NULL);

CREATE TABLE `e_payment_method` (
  `id` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `price` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `type` enum('before_delivery','on_delivery') COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `e_payment_method` (`id`, `name`, `price`, `priority`, `type`) VALUES
('cod',	'dobírka',	40,	40,	'on_delivery'),
('transfer',	'převodem na účet',	-24,	10,	'before_delivery'),
('personal',	'osobní odběr',	0,	30,	'on_delivery'),
('pay_zasilkovna',	'Zásilkovna',	40,	40,	'on_delivery');

CREATE TABLE `e_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `active` varchar(5) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `opt1` smallint(5) unsigned DEFAULT NULL,
  `opt2` smallint(5) unsigned DEFAULT NULL,
  `opt3` smallint(5) unsigned DEFAULT NULL,
  `opt4` smallint(5) unsigned DEFAULT NULL,
  `opt5` smallint(5) unsigned DEFAULT NULL,
  `ans1` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `ans2` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `ans3` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `ans4` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `ans5` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `item_image` (
  `home_art` mediumint(8) unsigned NOT NULL,
  `art_img_count` mediumint(9) unsigned NOT NULL,
  `extension` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  KEY `home_art` (`home_art`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2017-04-17 12:09:48