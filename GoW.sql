-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 25 Novembre 2015 à 00:34
-- Version du serveur: 5.5.46-MariaDB-1ubuntu0.14.04.2
-- Version de PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `GoW`
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
-- Structure de la table `cartes`
--

CREATE TABLE IF NOT EXISTS `cartes` (
  `idCarte` int(16) unsigned NOT NULL AUTO_INCREMENT COMMENT 'identifiant',
  `langue` varchar(3) CHARACTER SET utf8 NOT NULL COMMENT 'la langue de la carte (ISO 639)',
  `extLangue` varchar(16) CHARACTER SET utf8 DEFAULT NULL COMMENT 'extension de langue (IETF)',
  `niveau` enum('A1','A1.1','A1.2','A2','B1','B2','C1','C2') CHARACTER SET utf8 NOT NULL COMMENT 'Niveau CECRL',
  `categorie` enum('nom','nom propre','adjectif','adverbe','Expression idiomatique') CHARACTER SET utf8 NOT NULL COMMENT 'Une catégorie qui pourra être une aide',
  `idDruide` int(16) unsigned NOT NULL COMMENT 'auteur',
  `mot` varchar(128) CHARACTER SET utf8 NOT NULL,
  `dateCreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCarte`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Les cartes, attention nécessitent jointures' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `coeff_niveau_langue`
--

CREATE TABLE IF NOT EXISTS `coeff_niveau_langue` (
  `niveau_langue` enum('Débutant','Intermédiaire','Avancé','Natif') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `coeff` float NOT NULL,
  UNIQUE KEY `niveau_langue_2` (`niveau_langue`),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `coeff_statut`
--

INSERT INTO `coeff_statut` (`id`, `coeff`) VALUES
(1, 1),
(2, 1.25),
(3, 1.5);

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
  `userlvl` enum('easy','medium','hard') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'easy',
  `time` int(8) NOT NULL DEFAULT '0',
  `points` int(8) NOT NULL DEFAULT '0',
  `pointsSanction` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userlvl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `game_lvl`
--

INSERT INTO `game_lvl` (`userlvl`, `time`, `points`, `pointsSanction`) VALUES
('easy', 90, 10, 0),
('medium', 60, 20, 0),
('hard', 45, 40, 0);

-- --------------------------------------------------------

--
-- Structure de la table `mots_interdits`
--

CREATE TABLE IF NOT EXISTS `mots_interdits` (
  `idCarte` int(16) NOT NULL COMMENT 'le mot auquel il se rapporte',
  `mot` varchar(48) CHARACTER SET utf8 NOT NULL COMMENT 'le mot interdit',
  `ordre` int(4) NOT NULL COMMENT 'l''ordre du mot interdit',
  UNIQUE KEY `idCarte` (`idCarte`,`mot`),
  UNIQUE KEY `idCarte_2` (`idCarte`,`ordre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des mots tabous pour un mot';

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `idTheme` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `themeFR` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT 'la traductions ce sera pour plus tard',
  PRIMARY KEY (`idTheme`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='liste des thèmes' AUTO_INCREMENT=5 ;

--
-- Contenu de la table `themes`
--

INSERT INTO `themes` (`idTheme`, `themeFR`) VALUES
(1, 'Objets'),
(2, 'Formation'),
(3, 'Profession'),
(4, 'Art');

-- --------------------------------------------------------

--
-- Structure de la table `themes_cartes`
--

CREATE TABLE IF NOT EXISTS `themes_cartes` (
  `idCarte` int(16) NOT NULL,
  `idTheme` int(8) NOT NULL,
  UNIQUE KEY `idCarte` (`idCarte`,`idTheme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table de jointure';

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
