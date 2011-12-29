<?php
/**
* English language constants related to module information
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// Module Info
// The name of this module

global $icmsModule;
define("_MI_YUBIKEY_MD_NAME", "Yubikey");
define("_MI_YUBIKEY_MD_DESC", "Enables 2-factor authentication using Yubikey hardware tokens");

// Block
define("_MI_YUBIKEY_LOGIN", "Login");
define("_MI_YUBIKEY_LOGINDSC", "A replacement for the standard login block, with Yubikey support.");

// Preferences
define("_MI_YUBIKEY_SHOW_BREADCRUMB", "Show breadcrumb?");
define("_MI_YUBIKEY_SHOW_BREADCRUMB_DSC", "Toggles the horizontal navigation breadcrumb on or off");
define("_MI_YUBIKEY_CLIENT_ID", "Yubikey client ID");
define("_MI_YUBIKEY_CLIENT_IDDSC", "You must obtain a client ID from Yubico,
	visit https://upgrade.yubico.com/getapikey/");
define("_MI_YUBIKEY_API_KEY", "Yubikey secret API key");
define("_MI_YUBIKEY_API_KEYDSC", "You must obtain an API key from Yubico, visit
	https://upgrade.yubico.com/getapikey/");
define("_MI_YUBIKEY_TIMESTAMP_TOLERANCE", "Timestamp tolerance (+/-seconds)");
define("_MI_YUBIKEY_TIMESTAMP_TOLERANCEDSC", "The tolerance for timestamp verification, in seconds
	(range 0-86,400). Make sure your server time and time zone settings are correct.");
define("_MI_YUBIKEY_CURL_TIMEOUT", "CURL timeout (seconds)");
define("_MI_YUBIKEY_CURL_TIMEOUTDSC", "The time (seconds) before CURL gives up trying to contact
	the authentication server.");

// Other
define("_MI_YUBIKEY_TOKENS", "Tokens");
define("_MI_YUBIKEY_TEMPLATES", "Templates");
define("_MI_YUBIKEY_MANUAL", "Manual");