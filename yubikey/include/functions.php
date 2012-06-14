<?php
/**
* Common functions used by the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Get module admion link
 *
 * @todo to be move in icms core
 *
 * @param string $moduleName dirname of the moodule
 * @return string URL of the admin side of the module
 */

function yubikey_getModuleAdminLink() {
	$return = "<a href='" . ICMS_URL . "/modules/" . icms::$module->getVar('dirname') 
			. "/admin/index.php'>" . _MD_YUBIKEY_ADMIN_PAGE . "</a>";
}

/**
 * @todo to be move in icms core
 */
function yubikey_getModuleName($withLink = TRUE, $forBreadCrumb = FALSE) 
{
	$yubikeyModule = icms_getModuleInfo('yubikey');
	$yubikeyConfig = icms_getModuleConfig($yubikeyModule->getVar('dirname'));
	if (!icms_get_module_status('yubikey')) {
		return '';
	}

	if (!$withLink) {
		return $yubikeyModule->getVar('name');
	} else {
		$ret = ICMS_URL . '/modules/' . $yubikeyModule->getVar('dirname') . '/';
		return '<a href="' . $ret . '">' . $yubikeyModule->getVar('name') . '</a>';
	}
}

/**
 * Get URL of previous page
 *
 * @todo to be moved in ImpressCMS 1.2 core
 *
 * @param string $default default page if previous page is not found
 * @return string previous page URL
 */
function yubikey_getPreviousPage($default=FALSE) {
	global $impresscms;
	if (isset($impresscms->urls['previouspage'])) {
		return $impresscms->urls['previouspage'];
	} elseif($default) {
		return $default;
	} else {
		return ICMS_URL;
	}
}

/**
 * Get month name by its ID
 *
 * @todo to be moved in ImpressCMS 1.2 core
 *
 * @param int $month_id ID of the month
 * @return string month name
 */
function yubikey_getMonthNameById($month_id) {
	return Icms_getMonthNameById($month_id);
}