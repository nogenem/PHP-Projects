-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 07-Mar-2015 às 17:22
-- Versão do servidor: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `managsite`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `midia_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`link_id`),
  KEY `media_fk` (`midia_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1038 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `midia`
--

CREATE TABLE IF NOT EXISTS `midia` (
  `midia_id` int(11) NOT NULL AUTO_INCREMENT,
  `midia_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `midia_which` varchar(20) COLLATE utf8_bin NOT NULL,
  `midia_tab` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `midia_comments` text COLLATE utf8_bin,
  `midia_week_day` varchar(7) COLLATE utf8_bin DEFAULT NULL,
  `midia_date` date DEFAULT NULL,
  `user_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`midia_id`),
  KEY `user_id_fk` (`user_id_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=596 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `user_session_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `user_permissions` longtext CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`user_id`, `user`, `user_password`, `user_name`, `user_session_id`, `user_permissions`) VALUES
(1, 'Admin', '$2a$08$2sGQinTFe3GF/YqAYQ66auL9o6HeFCQryHdqUDvuEVN0J1vdhimii', 'Admin', 'lvmji3pdcak8qvt7ou2d057fn3', 'a:1:{i:0;s:3:"any";}');

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `link`
--
ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`midia_fk`) REFERENCES `midia` (`midia_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `midia`
--
ALTER TABLE `midia`
  ADD CONSTRAINT `midia_ibfk_1` FOREIGN KEY (`user_id_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
