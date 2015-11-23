-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Ven 20 Novembre 2015 à 17:34
-- Version du serveur: 5.5.43
-- Version de PHP: 5.5.29-1~dotdeb+7.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `gow`
--

-- --------------------------------------------------------

--
-- Structure de la table `arbitrage`
--

CREATE TABLE IF NOT EXISTS `arbitrage` (
  `arbitrageID` int(11) NOT NULL AUTO_INCREMENT,
  `enregistrementID` int(11) NOT NULL,
  `idDruide` int(11) NOT NULL,
  `tpsArbitrage` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `validation` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`arbitrageID`),
  KEY `validation` (`validation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `carte`
--

CREATE TABLE IF NOT EXISTS `carte` (
  `carteID` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idDruide` int(30) NOT NULL,
  `temps` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `niveau` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `langue` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mot` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tabou1` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tabou2` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tabou3` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tabou4` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tabou5` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tabou6` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`carteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `coeff_niveau_langue`
--

CREATE TABLE IF NOT EXISTS `coeff_niveau_langue` (
  `niveau_langue` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `coeff` float NOT NULL,
  KEY `niveau_langue` (`niveau_langue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `coeff_niveau_langue`
--

INSERT INTO `coeff_niveau_langue` (`niveau_langue`, `coeff`) VALUES
('Débutant', 1.5),
('Intermédiaire', 1.2),
('Avancé', 1),
('Natif', 0.5);

-- --------------------------------------------------------

--
-- Structure de la table `coeff_statut`
--

CREATE TABLE IF NOT EXISTS `coeff_statut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coeff` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `enregistrement`
--

CREATE TABLE IF NOT EXISTS `enregistrement` (
  `enregistrementID` int(11) NOT NULL AUTO_INCREMENT,
  `cheminEnregistrement` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idOracle` int(30) NOT NULL,
  `OracleLang` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tpsEnregistrement` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `carteID` int(11) NOT NULL,
  `nivcarte` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `validation` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`enregistrementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `game_lvl`
--

CREATE TABLE IF NOT EXISTS `game_lvl` (
  `userlvl` varchar(35) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `pointsSanction` int(11) NOT NULL,
  PRIMARY KEY (`userlvl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `notif`
--

CREATE TABLE IF NOT EXISTS `notif` (
  `userid` int(11) NOT NULL,
  `message` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `emetteur` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` int(11) NOT NULL,
  `game` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `time` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `parties`
--

CREATE TABLE IF NOT EXISTS `parties` (
  `partieID` int(11) NOT NULL AUTO_INCREMENT,
  `enregistrementID` int(11) NOT NULL,
  `tpsDevin` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idDevin` int(11) NOT NULL,
  `tpsdejeu` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `reussie` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`partieID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `sanctionCarte`
--

CREATE TABLE IF NOT EXISTS `sanctionCarte` (
  `idDevin` int(11) NOT NULL,
  `enregistrementID` int(11) NOT NULL,
  UNIQUE KEY `idDevin` (`idDevin`,`enregistrementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `score`
--

CREATE TABLE IF NOT EXISTS `score` (
  `scoreID` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(100) NOT NULL,
  `scoreGlobal` int(100) NOT NULL,
  `scoreOracle` int(100) NOT NULL,
  `scoreDruide` int(100) NOT NULL,
  `scoreDevin` int(100) NOT NULL,
  `langue` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_game_time` text NOT NULL,
  PRIMARY KEY (`scoreID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `useremail` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `userpass` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `userlang` varchar(32) NOT NULL,
  `valkey` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `userlang_game` varchar(100) NOT NULL,
  `photo` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userlvl` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user_niveau`
--

CREATE TABLE IF NOT EXISTS `user_niveau` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `userid` int(50) NOT NULL,
  `spoken_lang` varchar(100) NOT NULL,
  `niveau` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
