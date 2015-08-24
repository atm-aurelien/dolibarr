<?php
/* Copyright (C) 2003-2004	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2004		Sebastien Di Cintio		<sdicintio@ressource-toi.org>
 * Copyright (C) 2004		Benoit Mortier			<benoit.mortier@opensides.be>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 		\defgroup   facture     Module invoices
 *      \brief      Module pour gerer les factures clients et/ou fournisseurs
 *      \file       htdocs/core/modules/modFacture.class.php
 *		\ingroup    facture
 *		\brief      Fichier de la classe de description et activation du module Facture
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Class to describe module customer invoices
 */
class modMultidevises extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $conf;

		$this->db = $db;
		$this->numero = 90;

		$this->family = "financial";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Application de 2 devises pour les propales, les commandes, les factures, les tiers et les règlements.";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='bill';

		// Data directories to create when module is enabled
		$this->dirs = array("/multidevis/temp");

		// Dependencies
		$this->depends = array("modSociete");
		$this->requiredby = array("modComptabilite","modAccounting");
		$this->conflictwith = array();
		$this->langfiles = array("bills","companies","compta","products");

		// Config pages
		$this->config_page_url = array("multidevises.php");

		// Constantes
		$this->const = array();
		$r=0;

		// Boxes
		//$this->boxes = array(0=>array(1=>'box_factures_imp.php'),1=>array(1=>'box_factures.php'));
		$this->boxes = array(
				
		);

		// Permissions
		$this->rights = array();
		$this->rights_class = 'multidevises';
		$r=0;



		// Exports
		//--------
		$r=1;

	}


	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'newboxdefonly', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $conf,$langs;

		// Remove permissions and default values
		$this->remove($options);



		$sql = array(
				
		);

		return $this->_init($sql,$options);
	}
}
