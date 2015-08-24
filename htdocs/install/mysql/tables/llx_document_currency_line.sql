--
-- Structure de la table `llx_devise_document_ligne`
--

CREATE TABLE IF NOT EXISTS `llx_devise_document_ligne` (
`rowid` int(11) NOT NULL,
  `element_type` varchar(25) NOT NULL,
  `element_id` int(11) NOT NULL,
  `pu_ht` double NOT NULL,
  `montant_ht` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
