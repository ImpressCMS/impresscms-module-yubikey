<?php
/**
 * Functions to edit and display the Yubikey login block.
 *
 * @copyright	http://smartfactory.ca The SmartFactory
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @author		marcan aka Marc-Andre Lanciault <marcan@smartfactory.ca>
 * @author		Madfish
 * @since		1.0
 * @package		news
 * @version		$Id$
 */

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Prepares the Yubikey login block for display
 *
 * @param array $options
 * @return string
 */

function yubikey_login_show($options) {
	
	$yubikeyModule = icms_getModuleInfo('yubikey');
	global $icmsUser, $icmsConfig, $xoTheme;
	$block = array();
	
	include_once(ICMS_ROOT_PATH . '/modules/' . $yubikeyModule->getVar('dirname') . '/include/common.php');
	$xoTheme->addStylesheet(ICMS_URL . '/modules/' . $yubikeyModule->getVar('dirname') . '/module.css');
	
	// Yubikey settings
	$block['yubikey'] = _MB_YUBIKEY;
	$block['yubikey_login'] = _MB_YUBIKEY_LOGIN;
	$block['yubikey_login_display_mode'] = $options[0];
	
	// The rest is standard system login block	
	if (!$icmsUser) {
		$block['lang_username'] = _USERNAME;
		$block['unamevalue'] = "";
		if (isset($_COOKIE[$icmsConfig['usercookie']])) {
			$block['unamevalue'] = $_COOKIE[$icmsConfig['usercookie']];
		}
		$block['lang_password'] = _PASSWORD;
		$block['lang_login'] = _LOGIN;
		$block['lang_lostpass'] = _MB_SYSTEM_LPASS;
		$block['lang_registernow'] = _MB_SYSTEM_RNOW;
		$block['lang_rememberme'] = _MB_SYSTEM_REMEMBERME;
		$block['lang_youoid'] = _MB_SYSTEM_OPENID_URL;
		$block['lang_login_oid'] = _MB_SYSTEM_OPENID_LOGIN;
		$block['lang_back2normoid'] = _MB_SYSTEM_OPENID_NORMAL_LOGIN;
		if ($icmsConfig['use_ssl'] == 1 && $icmsConfig['sslloginlink'] != '') {
			$block['sslloginlink'] = "<a href=\"javascript:openWithSelfMain('".$icmsConfig['sslloginlink']."', 'ssllogin', 300, 200);\">"._MB_SYSTEM_SECURE."</a>";
		}

		$config_handler =& xoops_gethandler('config');
		$icmsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);

		if ($icmsConfigUser['allow_register'] == 1) {
			$block['registration'] = $icmsConfigUser['allow_register'];
		}

		if ($icmsConfigUser['remember_me'] == 1) {
			$block['rememberme'] = $icmsConfigUser['remember_me'];
		}

		$xoopsAuthConfig =& $config_handler->getConfigsByCat(XOOPS_CONF_AUTH);
		if ($xoopsAuthConfig['auth_openid']) {
			$block['auth_openid'] = true;
		}
		return $block;
	}
	return false;
}

/**
 * Edit options for the Yubikey login block
 *
 * @param array $options
 * @return string
 */

function yubikey_login_edit($options) {

	$yubikeyModule = icms_getModuleInfo('yubikey');
	include_once(ICMS_ROOT_PATH . '/modules/' . $yubikeyModule->getVar('dirname') . '/include/common.php');

	// Set block display configuration - this controls which login fields will be displayed	
	$display_mode_array = array(
		0 => _MB_YUBIKEY_LOGIN_OPTION_0, // Display username, password fields, no Yubikey links
		1 => _MB_YUBIKEY_LOGIN_OPTION_1, // (Default) Display username, password fields + link to Yubikey login page
		2 => _MB_YUBIKEY_LOGIN_OPTION_2, // Display username, password and Yubikey (optional) fields
		3 => _MB_YUBIKEY_LOGIN_OPTION_3  // Display password and Yubikey (required) fields
		);
	
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_YUBIKEY_LOGIN_DISPLAY_MODE . '</td>';
	
	// Parameters XoopsFormSelect: ($caption, $name, $value = null, $size = 1, $multiple = false)
	$form_select_display_mode = new XoopsFormSelect('', 'options[0]', $options[0], '1', false);
	$form_select_display_mode->addOptionArray($display_mode_array);
	
	$form .= '<td>' . $form_select_display_mode->render() . '</td>';
	$form .= '</tr></table>';

	return $form;
}