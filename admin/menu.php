<?php
/**
* Configuring the amdin side menu for the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

$i = -1;

$i++;
$adminmenu[$i]['title'] = _MI_YUBIKEY_TOKENS;
$adminmenu[$i]['link'] = 'admin/token.php';

global $icmsConfig;
$yubikeyModule = icms_getModuleInfo('yubikey');

if (isset($yubikeyModule)) {

	$i = -1;
	
	$i++;
	$headermenu[$i]['title'] = _CO_ICMS_GOTOMODULE;
	$headermenu[$i]['link'] = ICMS_URL . '/modules/' . $yubikeyModule->dirname();

	$i++;
	$headermenu[$i]['title'] = _PREFERENCES;
	$headermenu[$i]['link'] = '../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
			. $yubikeyModule->mid();
	
	$i++;
	$headermenu[$i]['title'] = _MI_YUBIKEY_TEMPLATES;
	$headermenu[$i]['link'] = '../../system/admin.php?fct=tplsets&op=listtpl&tplset='
			. $icmsConfig['template_set'] . '&moddir=' . $yubikeyModule->dirname();

	$i++;
	$headermenu[$i]['title'] = _CO_ICMS_UPDATE_MODULE;
	$headermenu[$i]['link'] = ICMS_URL 
			. '/modules/system/admin.php?fct=modulesadmin&op=update&module='
			. $yubikeyModule->dirname();

	$i++;
	$headermenu[$i]['title'] = _MODABOUT_ABOUT;
	$headermenu[$i]['link'] = ICMS_URL . '/modules/' . $yubikeyModule->dirname()
			. '/admin/about.php';

	$i++;
	$headermenu[$i]['title'] = _MI_YUBIKEY_MANUAL;
	$headermenu[$i]['link'] = ICMS_URL . '/modules/' . $yubikeyModule->dirname()
			. '/extras/yubikey_module_manual.pdf';
}