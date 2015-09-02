<?php
/* Copyright (C) 2003-2004	Rodolphe Quiedeville		<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011	Laurent Destailleur			<eldy@users.sourceforge.net>
 * Copyright (C) 2005		Eric Seigne					<eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2012	Regis Houssin				<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand (Resultic)	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2012-2013  Juanjo Menent				<jmenent@2byte.es>
 * Copyright (C) 2014		Teddy Andreotti				<125155@supinfo.com>
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
 *      \file       htdocs/admin/facture.php
 *		\ingroup    facture
 *		\brief      Page to setup invoice module
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/openid.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/multidevises.lib.php';
//require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';

$langs -> load("admin");
$langs -> load("errors");
$langs -> load('other');
//$langs -> load('bills');
$langs -> load('multidevises');

if (!$user -> admin)
	accessforbidden();

$action = GETPOST('action', 'alpha');
$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scandir', 'alpha');
$type = 'multidevises';

if(isset($_REQUEST['check'])) {
	$OpenID = new SimpleOpenID;
	$code = $OpenID -> CURL_Request(dolibarr_get_const($db, 'MULTIDEVISES_API_URL'));
	if (dolibarr_get_const($db, 'MULTIDEVISES_API_MODE') == 'JSON') {
		$json = json_decode($code, 1);

		//Listing
		$basePath = dolibarr_get_const($db, 'MULTIDEVISES_API_BASEPATH');
		$tBases = explode('/', $basePath);
		$jsonBase = $json;
		foreach ($tBases as $subPath) {
			if ($subPath)
				$jsonBase = $jsonBase[$subPath];
		}
		$base = $jsonBase;

		$coeff = 0;

		$pathRates = dolibarr_get_const($db, 'MULTIDEVISES_API_RATESPATH');
		$tRates = explode('/', $pathRates);
		$jsonRates = $json;
		foreach ($tRates as $subPath) {
			if ($subPath == '[code]') {
				foreach ($jsonRates as $code => $rate) {
					//Les données sont relatives au USD. Il faut les mettre dans le taux de la devise par défaut
					if (!$coeff) {
						$coeff = 1 / $jsonRates[$conf -> currency];
					}

					$sql = "UPDATE " . MAIN_DB_PREFIX . "c_currencies SET current_rate='" . ($coeff * $rate) . "' WHERE code_iso='$code'";
					$db -> query($sql);
				}
				break;
			} elseif ($subPath) {
				$jsonRates = $jsonRates[$subPath];
			}
		}
	}
	
	header("Location: ?show");
}

if(isset($_REQUEST['updateRate'])) {
 	$sql = "UPDATE " . MAIN_DB_PREFIX . "c_currencies SET current_rate='" . $_REQUEST['newRate'] . "' WHERE code_iso='".$_REQUEST['updateRate']."'";
	$db -> query($sql);
	header("Location: ?show");
 }

/*
 * View
 */
 llxHeader('',$langs->trans("Multidevises"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MultiDevisesConfig"),$linkback,'title_setup');

$head = multidevises_admin_prepare_head();

$tab='api';
if(isset($_REQUEST['main'])) $tab='general';
if(isset($_REQUEST['show'])) $tab='list';

/*
 * Saving
 */
 if(isset($_REQUEST['MultiDevisesAPIURL'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_API_URL',$_REQUEST['MultiDevisesAPIURL'],'chaine',0,'',$conf->entity);
 }
 if(isset($_REQUEST['MultiDevisesBasePath'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_API_BASEPATH',$_REQUEST['MultiDevisesBasePath'],'chaine',0,'',$conf->entity);
 }
  if(isset($_REQUEST['MultiDevisesDatePath'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_API_DATEPATH',$_REQUEST['MultiDevisesDatePath'],'chaine',0,'',$conf->entity);
 }
 if(isset($_REQUEST['MultiDevisesRatesPath'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_API_RATESPATH',$_REQUEST['MultiDevisesRatesPath'],'chaine',0,'',$conf->entity);
 }
  if(isset($_REQUEST['MultidevisesRecupMode'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_API_MODE',$_REQUEST['MultidevisesRecupMode'],'chaine',0,'',$conf->entity);
 }
  if(isset($_REQUEST['MultidevisesRateHisto'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_RATE_HISTO',$_REQUEST['MultidevisesRateHisto'],'chaine',0,'',$conf->entity);
 }
 if(isset($_REQUEST['MultidevisesUpdateConv'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_UPDATE_CONV',$_REQUEST['MultidevisesUpdateConv'],'chaine',0,'',$conf->entity);
 }
  if(isset($_REQUEST['MultidevisesCalcRate'])) {
 	dolibarr_set_const($db,'MULTIDEVISES_CALC_RATE',$_REQUEST['MultidevisesCalcRate'],'chaine',0,'',$conf->entity);
 }


dol_fiche_head($head, $tab, $langs->trans("Multidevises"), 0, 'invoice');

if(isset($_REQUEST['api']) || !$_GET) {
?>
<form method="post">
	<table class="noborder" width="100%">
		<tr class="liste_titre">
			<td colspan="2">
				<?php echo $langs->trans("MultiDevisesAPIConf") ?>
			</td>
		</tr>
		<tr class="impair">
			<td>
				<?php echo $langs->trans("MultiDevisesAPIURL") ?><br/>
				<i>Vous pouvez créer un compte gratuit sur le site <a href="https://openexchangerates.org/signup/free" target="_blank">OpenExchangeRates</a> (en anglais).</i>
			</td>
			<td>
				<input type="text" name="MultiDevisesAPIURL" value="<?php echo dolibarr_get_const($db, 'MULTIDEVISES_API_URL'); ?>"/>
			</td>
		</tr>
		<tr class="pair">
			<td>
				<?php echo $langs->trans("MultiDevisesAPIGetMode") ?>
			</td>
			<td>
				<select name="MultidevisesRecupMode">
					<option value="XML"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_API_MODE') == 'XML' ? ' selected="selected"' : ''; ?>>XML</option>
					<option value="JSON"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_API_MODE') == 'JSON' ? ' selected="selected"' : ''; ?>>JSON</option>
				</select>
			</td>
		</tr>
		<tr class="impair">
			<td>
				<?php echo $langs->trans("MultiDevisesAPIBasePath") ?>
			</td>
			<td>
				<input type="text" name="MultiDevisesBasePath" value="<?php echo dolibarr_get_const($db, 'MULTIDEVISES_API_BASEPATH'); ?>"/>
			</td>
		</tr>
		<tr class="pair">
			<td>
				<?php echo $langs->trans("MultiDevisesAPIDatePath") ?>
			</td>
			<td>
				<input type="text" name="MultiDevisesDatePath" value="<?php echo dolibarr_get_const($db, 'MULTIDEVISES_API_DATEPATH'); ?>"/>
			</td>
		</tr>
		<tr class="impair">
			<td>
				<?php echo $langs->trans("MultiDevisesAPIRatesPath") ?>
			</td>
			<td>
				<input type="text" name="MultiDevisesRatesPath" value="<?php echo dolibarr_get_const($db, 'MULTIDEVISES_API_RATESPATH'); ?>"/>
			</td>
		</tr>
	</table>
	<input type="submit" value="<?php echo $langs->trans("MultiDevisesSave") ?>"/>
</form>
<?php
}

if(isset($_REQUEST['main'])) {
?>
<form method="post" id="formMain">
	<table class="noborder" width="100%">
		<tr class="liste_titre">
			<td colspan="2">
				<?php echo $langs->trans("MultiDevisesRates") ?>
			</td>
		</tr>
		<tr class="pair">
			<td>
				<?php echo $langs->trans("MultiDevisesRatesSave") ?>
			</td>
			<td>
				<select name="MultidevisesRateHisto" onchange="$('#formMain').submit();">
					<option value="0"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_RATE_HISTO') == '0' ? ' selected="selected"' : ''; ?>><?php echo $langs->trans("MultiDevisesRatesSaveNever") ?></option>
					<option value="1"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_RATE_HISTO') == '1' ? ' selected="selected"' : ''; ?>><?php echo $langs->trans("MultiDevisesRatesSave1Month") ?></option>
					<option value="6"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_RATE_HISTO') == '6' ? ' selected="selected"' : ''; ?>><?php echo $langs->trans("MultiDevisesRatesSave6Month") ?></option>
					<option value="12"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_RATE_HISTO') == '12' ? ' selected="selected"' : ''; ?>><?php echo $langs->trans("MultiDevisesRatesSave12Month") ?></option>
					<option value="-1"<?php echo dolibarr_get_const($db, 'MULTIDEVISES_RATE_HISTO') == '-1' ? ' selected="selected"' : ''; ?>><?php echo $langs->trans("MultiDevisesRatesSaveForever") ?></option>
				</select>
			</td>
		</tr>
		<tr class="impair">
			<td>
				<?php echo $langs->trans("MultiDevisesAllowUpdateRate") ?> 
			</td>
			<td>
				<input type="hidden" name="MultidevisesUpdateConv" id="MultiDevisesUpdateConv" value="<?php echo dolibarr_get_const($db, 'MULTIDEVISES_UPDATE_CONV'); ?>"/>
				<?php if(dolibarr_get_const($db, 'MULTIDEVISES_UPDATE_CONV')=='1') { ?>
				<a href="#" onclick="$('#MultiDevisesUpdateConv').val(0);$('#formMain').submit();"><?php print img_picto($langs -> trans("Activated"), 'switch_on'); ?></a>
				<?php } else { ?>
				<a href="#" onclick="$('#MultiDevisesUpdateConv').val(1);$('#formMain').submit();"><?php print img_picto($langs -> trans("Activated"), 'switch_off'); ?></a>
				<?php } ?>
			</td>
		</tr>
		<tr class="pair">
			<td>
				<?php echo $langs->trans("MultiDevisesCalcPrices") ?> 
			</td>
			<td>
				<input type="hidden" name="MultidevisesCalcRate" id="MultidevisesCalcRate" value="<?php echo dolibarr_get_const($db, 'MULTIDEVISES_CALC_RATE'); ?>"/>
				<?php if(dolibarr_get_const($db, 'MULTIDEVISES_CALC_RATE')=='1') { ?>
				<a href="#" onclick="$('#MultidevisesCalcRate').val(0);$('#formMain').submit();"><?php print img_picto($langs -> trans("Activated"), 'switch_on'); ?></a>
				<?php } else { ?>
				<a href="#" onclick="$('#MultidevisesCalcRate').val(1);$('#formMain').submit();"><?php print img_picto($langs -> trans("Activated"), 'switch_off'); ?></a>
				<?php } ?>
			</td>
		</tr>

	</table>
</form>
<?php
}

if(isset($_REQUEST['show'])) {
	?>

<form method="post" id="formMain">
	<table class="noborder" width="100%">
		<tr class="liste_titre">
			<td><?php echo $langs->trans("MultiDevisesCurrencies") ?> </td>
			<td><?php echo $langs->trans("MultiDevisesISO") ?></td>
			<td><?php echo $langs->trans("MultiDevisesCurrentRate") ?></td>
		</tr>
		<?php
		$i=0;
		$sql="SELECT * FROM " . MAIN_DB_PREFIX . "c_currencies ORDER BY label ASC";
		$resultset=$db->query($sql);
		if($resultset) {
			while($row= $db->fetch_object($resultset)) {
				$class='pair';	
				if($i%2) $class='impair';
				$i++;
				?>
				<tr class="<?php echo $class ?>">
					<td>
						<?php echo $row->label ?>
					</td>
					<td>
						<?php echo $row->code_iso ?>
					</td>
					<td class="right">
						<span id="show-<?php echo $row->code_iso ?>"><?php echo $row->current_rate ?> <a href="javascript:editRate('<?php echo $row->code_iso ?>')"><?php echo img_edit($langs->trans('Modify')) ?></a></span>
						<span style="display:none;" id="edit-<?php echo $row->code_iso ?>">
							<input type="hidden" name="updateRate" value="<?php echo $row->code_iso ?>"/>
							<input type="text" name="newRate" value="<?php echo $row->current_rate ?>"/>
							<input type="button" onclick="saveRate('<?php echo $row->code_iso ?>');" value="Modifier"/>
							<input type="button" onclick="showRate('<?php echo $row->code_iso ?>');" value="Annuler"/>
						</span>
					</td>
				</tr>
				<?php
			}
		}else{
			dol_syslog("Error on currencies listing");
		}
		?>
	</table>
	<input type="button" value="<?php echo $langs->trans("MultiDevisesRatesRefresh") ?>" onclick="location.href='?show&check'"/>
	<script>
		function editRate(code) {
			$("#show-"+code).hide();
			$("#edit-"+code).show();
			$("#edit-"+code+' input[name="newRate"]').focus().select();
		}
		function showRate(code) {
			$("#show-"+code).show();
			$("#edit-"+code).hide();
		}
		function saveRate(code) {
			location.href='?show&updateRate='
				+$('#edit-'+code+' input[name="updateRate"]').val()
				+'&newRate='+$('#edit-'+code+' input[name="newRate"]').val();
		}
		$('input[name="newRate"]').keypress(function(e) {
		    if(e.which == 13) {
		       saveRate($(this).parent().find('input[name="updateRate"]').val());
		    }
		});
	</script>
</form>
<?php
}

llxFooter();