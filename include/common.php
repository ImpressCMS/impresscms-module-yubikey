<?php
/**
* Common file of the module included on all pages of the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

if(!defined("YUBIKEY_DIRNAME")) define("YUBIKEY_DIRNAME", $modversion['dirname']
		= basename(dirname(dirname(__FILE__))));
if(!defined("YUBIKEY_URL")) define("YUBIKEY_URL", ICMS_URL.'/modules/'.YUBIKEY_DIRNAME.'/');
if(!defined("YUBIKEY_ROOT_PATH")) define("YUBIKEY_ROOT_PATH", ICMS_ROOT_PATH.'/modules/'
		. YUBIKEY_DIRNAME . '/');
if(!defined("YUBIKEY_IMAGES_URL")) define("YUBIKEY_IMAGES_URL", YUBIKEY_URL . 'images/');
if(!defined("YUBIKEY_ADMIN_URL")) define("YUBIKEY_ADMIN_URL", YUBIKEY_URL . 'admin/');

// Include the common language file of the module
icms_loadLanguageFile('yubikey', 'common');

include_once(YUBIKEY_ROOT_PATH . "include/functions.php");

// Creating the module object to make it available throughout the module
$yubikeyModule = icms_getModuleInfo(YUBIKEY_DIRNAME);
if (is_object($yubikeyModule)){
	$yubikey_moduleName = $yubikeyModule->getVar('name');
}

// Find if the user is admin of the module and make this info available throughout the module
$yubikey_isAdmin = icms_userIsAdmin(YUBIKEY_DIRNAME);

// Creating the module config array to make it available throughout the module
$yubikeyConfig = icms_getModuleConfig(YUBIKEY_DIRNAME);

// creating the icmsPersistableRegistry to make it available throughout the module
global $icmsPersistableRegistry;
$icmsPersistableRegistry = IcmsPersistableRegistry::getInstance();