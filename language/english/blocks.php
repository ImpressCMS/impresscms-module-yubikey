<?php
/**
* English language constants used in blocks of the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// Yubikey login block
define("_MB_YUBIKEY", "Yubikey (admin):");
define("_MB_YUBIKEY_LOGIN", "Yubikey (Admin)");
define("_MB_YUBIKEY_LOGINDSC", "Replacement for the standard login block, supports Yubikeys.");
define("_MB_YUBIKEY_LOGIN_DISPLAY_MODE", "Display mode:");
define("_MB_YUBIKEY_LOGIN_OPTION_1", "Username, password (no Yubikey login)");
define("_MB_YUBIKEY_LOGIN_OPTION_2", "Username, password, link to Yubikey login page");
define("_MB_YUBIKEY_LOGIN_OPTION_3", "Username, password, Yubikey");
define("_MB_YUBIKEY_LOGIN_OPTION_4", "Password, Yubikey (Yubikey required)");