<?php
/**
* English language constants commonly used in the module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// token
define("_CO_YUBIKEY_TOKEN_YUBIKEY_ENABLED", "Two factor authentication required?");
define("_CO_YUBIKEY_TOKEN_YUBIKEY_ENABLED_DSC", " Toggle Yubikey authentication on or off. If
	enabled, then Yubikey hardware authentication is REQUIRED in order to login to this account.");
define("_CO_YUBIKEY_TOKEN_PUBLIC_ID", "Public ID");
define("_CO_YUBIKEY_TOKEN_PUBLIC_ID_DSC", " Discharge the Yubikey into this field. The ID will be 
	extracted and the output tested against the validation server. Alternatively, type in the
	STATIC portion of the string emitted by the Yubikey.");
define("_CO_YUBIKEY_TOKEN_USER_ID", "User name");
define("_CO_YUBIKEY_TOKEN_USER_ID_DSC", " The user account associated with this Yubikey. User 
	accounts can have several keys associated with it (ie. spares) but each Yubikey can only be
	associated with one user account.");
define("_CO_YUBIKEY_TOKEN_UID", "User ID");
define("_CO_YUBIKEY_TOKEN_ENABLE", "Enable Yubikey authentication.");
define("_CO_YUBIKEY_TOKEN_ENABLED", "Yubikey authentication enabled.");
define("_CO_YUBIKEY_TOKEN_DISABLED", "Yubikey authentication disabled.");
define("_CO_YUBIKEY_TOKEN_DISABLE", "Disable Yubikey authentication.");

// login
define("_CO_YUBIKEY_LOGIN_SUCCESSFUL", "Login successful.");
define("_CO_YUBIKEY_LOGIN_FAILED", "Login failed.");
define("_CO_YUBIKEY_AUTHENTICATION_REQUIRED", "Yubikey authentication required.");
define("_CO_YUBIKEY_TWO_FACTOR_AUTHENTICATION_REQUIRED", "Two-factor authentication required");
define("_CO_YUBIKEY_PASSWORD", "1. Password:");
define("_CO_YUBIKEY_TRIGGER_YOUR_YUBIKEY", "2. Trigger your Yubikey in the field below.");

// Errors
define("_CO_YUBIKEY_ERROR_DUPLICATE_KEYS_NOT_ALLOWED", "Error: Key already exists, duplicate keys
	are not allowed");