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

