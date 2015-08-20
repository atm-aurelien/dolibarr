<?php

/**
 * Return array head with list of tabs to view object informations.
 *
 * @return array head array with tabs
 */
function multidevises_admin_prepare_head()
{
	global $langs, $conf, $user;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/admin/multidevises.php?api';
	$head[$h][1] = $langs->trans("API");
	$head[$h][2] = 'api';
	$h++;
	
	$head[$h][0] = DOL_URL_ROOT.'/admin/multidevises.php?main';
	$head[$h][1] = $langs->trans("General");
	$head[$h][2] = 'general';
	$h++;
	
	$head[$h][0] = DOL_URL_ROOT.'/admin/multidevises.php?show';
	$head[$h][1] = $langs->trans("Listing");
	$head[$h][2] = 'list';
	$h++;

	return $head;
}
