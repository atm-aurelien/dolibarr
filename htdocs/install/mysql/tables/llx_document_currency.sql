-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 19 Août 2015 à 17:10
-- Version du serveur :  5.6.25-0ubuntu0.15.04.1
-- Version de PHP :  5.6.4-4ubuntu6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `dolibarr_dev`
--

-- --------------------------------------------------------

--
-- Structure de la table `llx_devise_document`
--

CREATE TABLE IF NOT EXISTS `llx_devise_document` (
`rowid` int(11) NOT NULL,
  `element_type` varchar(25) NOT NULL,
  `element_id` int(11) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

