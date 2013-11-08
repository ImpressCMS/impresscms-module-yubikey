<?php
// $Id: checklogin.php 12313 2013-09-15 21:14:35Z skenow $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.xoops.org/ http://jp.xoops.org/  http://www.myweb.ne.jp/  //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //
/**
 * The check login include file
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category
 * @package		Members
 * @subpackage	Users
 * @since		XOOPS
 * @version		$Id: checklogin.php 12313 2013-09-15 21:14:35Z skenow $
 */

defined('ICMS_ROOT_PATH') || exit();

icms_loadLanguageFile('core', 'user');
$uname = !isset($_POST['uname']) ? '' : trim($_POST['uname']);
$pass = !isset($_POST['pass']) ? '' : trim($_POST['pass']);
/**
 * Commented out for OpenID , we need to change it to make a better validation if OpenID is used
 */
/*if ($uname == '' || $pass == '') {
 redirect_header(ICMS_URL.'/user.php', 1, _US_INCORRECTLOGIN);
 exit();
 }*/
$member_handler = icms::handler('icms_member');

icms_loadLanguageFile('core', 'auth');
$icmsAuth =& icms_auth_Factory::getAuthConnection(icms_core_DataFilter::addSlashes($uname));

$uname4sql = addslashes(icms_core_DataFilter::stripSlashesGPC($uname));
$pass4sql = icms_core_DataFilter::stripSlashesGPC($pass);

if (empty($user) || !is_object($user)) {
	$user =& $icmsAuth->authenticate($uname4sql, $pass4sql);
}

//////////////////////////////////////////////////////////////////////////
//////////////////// YUBIKEY AUTHENTICATION INTERRUPT ////////////////////
//////////////////////////////////////////////////////////////////////////

// Only run these tests if the file has been included from outside the Yubikey login page
// AND if the site is turned on (Yubikey authentication is disabled when the site is turned
// off, in order to prevent lock-outs. Obviously, this is not an ideal situation and it will
// be changed in the next version of the module).

global $icmsConfig;

// $yubikey_login_page is a flag that is set by the the Yubikey login page, to indicate whether this
// file was included from there, or elsewhere
if (!$yubikey_login_page && !$icmsConfig['closesite']) {

	$is_email_address = FALSE;
	$yubikeyModule = $user_criteria = $user_list = $user_id = $yubikey_authentication_required = '';

	// Check if the Yubikey module is installed
	if (icms_get_module_status("yubikey")) {
		
		$yubikeyModule = icms_getModuleInfo('yubikey');

		// Lookup the uid of this user, test whether user is logging in with login_name or email address
		$is_email_address = strpos($uname4sql, '@');
		if ($is_email_address) {
			$user_criteria = icms_buildCriteria(array('email' => $uname4sql));
		} else {
			$user_criteria = icms_buildCriteria(array('login_name' => $uname4sql));
		}
				
		$user_list = $member_handler->getUserList($user_criteria);

		if ($user_list) {
			$user_id = array_shift(array_keys($user_list));

			// Check if there is an enabled Yubikey associated with this username (login_name)
			$yubikey_token_handler = icms_getModuleHandler('token', $yubikeyModule->getVar('dirname'),
					'yubikey');
			$yubikey_criteria = icms_buildCriteria(array('user_id' => $user_id,
					'yubikey_enabled' => '1'));
			$yubikey_authentication_required = $yubikey_token_handler->getCount($yubikey_criteria);

			if ($yubikey_authentication_required > 0) {

				// Redirect Yubikey-enabled accounts to the Yubikey login page
				unset($yubikey_authentication_required);
				icms_loadLanguageFile('yubikey', 'common');
				redirect_header(ICMS_URL . '/modules/' . $yubikeyModule->getVar('dirname') . '/token.php', 2,
						_CO_YUBIKEY_AUTHENTICATION_REQUIRED);
				exit;
			}
		}
	}
} else {
	unset ($yubikey_login_page);
}

// If any of the above tests fail, continue with normal login procedure

/////////////////////////////////////////////////////////////////////////////////////////
//////////////////// END YUBIKEY AUTHENTICATION INTERRUPT ///////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

if (FALSE != $user) {
	if (0 == $user->getVar('level')) {
		redirect_header(ICMS_URL . '/', 5, _US_NOACTTPADM);
		exit();
	}
	if ($icmsConfigPersona['multi_login']) {
		if (is_object($user)) {
			$online_handler = icms::handler('icms_core_Online');
			$online_handler->gc(300);
			$onlines =& $online_handler->getAll();
			foreach ($onlines as $online) {
				if ($online['online_uid'] == $user->getVar('uid')) {
					$user = FALSE;
					redirect_header(ICMS_URL . '/', 3, _US_MULTLOGIN);
				}
			}
			if (is_object($user)) {
				$online_handler->write(
					$user->getVar('uid'),
					$user->getVar('uname'),
					time(),
					0,
					$_SERVER['REMOTE_ADDR']
				);
			}
		}
	}
	if ($icmsConfig['closesite'] == 1) {
		$allowed = FALSE;
		foreach ( $user->getGroups() as $group) {
			if (in_array($group, $icmsConfig['closesite_okgrp']) || ICMS_GROUP_ADMIN == $group) {
				$allowed = TRUE;
				break;
			}
		}
		if (!$allowed) {
			redirect_header(ICMS_URL . '/', 1, _NOPERM);
			exit();
		}
	}

	$user->setVar('last_login', time());
	if (!$member_handler->insertUser($user)) {}
	// Regenrate a new session id and destroy old session
	session_regenerate_id(TRUE);
	$_SESSION = array();
	$_SESSION['xoopsUserId'] = $user->getVar('uid');
	$_SESSION['xoopsUserGroups'] = $user->getGroups();
	if ($icmsConfig['use_mysession'] && $icmsConfig['session_name'] != '') {
		setcookie($icmsConfig['session_name'], session_id(), time()+(60 * $icmsConfig['session_expire']), '/',  '', 0);
	}
	$_SESSION['xoopsUserLastLogin'] = $user->getVar('last_login');
	if (!$member_handler->updateUserByField($user, 'last_login', time())) {}
	$user_theme = $user->getVar('theme');
	if (in_array($user_theme, $icmsConfig['theme_set_allowed'])) {
		$_SESSION['xoopsUserTheme'] = $user_theme;
	}
	if (!empty($_POST['xoops_redirect']) && !strpos($_POST['xoops_redirect'], 'register')) {
		$_POST['xoops_redirect'] = trim($_POST['xoops_redirect']);
		$parsed = parse_url(ICMS_URL);
		$url = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : 'http://';
		if (isset($parsed['host'])) {
			$url .= $parsed['host'];
			if (isset($parsed['port'])) {
				$url .= ':' . $parsed['port'];
			}
		} else {
			$url .= $_SERVER['HTTP_HOST'];
		}
		if (@$parsed['path']) {
			if (strncmp($parsed['path'], $_POST['xoops_redirect'], strlen($parsed['path']))) {
				$url .= $parsed['path'];
			}
		}
		$url .= $_POST['xoops_redirect'];
	} else {
		$url = ICMS_URL . '/';
	}
	if ($pos = strpos($url, '://')) {
		$xoopsLocation = substr(ICMS_URL, strpos(ICMS_URL, '://') + 3);
		if (substr($url, $pos + 3, strlen($xoopsLocation)) != $xoopsLocation) {
			$url = ICMS_URL;
		} elseif (substr($url, $pos + 3, strlen($xoopsLocation)+1) == $xoopsLocation . '.') {
			$url = ICMS_URL;
		}
		if (substr($url, 0, strlen(ICMS_URL)*2) ==  ICMS_URL . ICMS_URL) {
			$url = substr($url, strlen(ICMS_URL));
		}
	}

	// autologin hack V3.1 GIJ (set cookie)
	$secure = substr(ICMS_URL, 0, 5) == 'https' ? 1 : 0; // we need to secure cookie when using SSL
	$icms_cookie_path = defined('ICMS_COOKIE_PATH') ? ICMS_COOKIE_PATH :
	preg_replace( '?http://[^/]+(/.*)$?' , "$1" , ICMS_URL );
	if ($icms_cookie_path == ICMS_URL) $icms_cookie_path = '/';
	if (!empty($_POST['rememberme'])) {
		$expire = time() + (defined('ICMS_AUTOLOGIN_LIFETIME') ? ICMS_AUTOLOGIN_LIFETIME : 604800) ; // 1 week default
		setcookie('autologin_uname', $user->getVar('login_name'), $expire, $icms_cookie_path, '', $secure, 0);
		$Ynj = date('Y-n-j') ;
		setcookie('autologin_pass', $Ynj . ':' . md5($user->getVar('pass') . ICMS_DB_PASS . ICMS_DB_PREFIX . $Ynj),
		$expire, $icms_cookie_path, '', $secure, 0);
	}
	// end of autologin hack V3.1 GIJ

	// Perform some maintenance of notification records
	$notification_handler = icms::handler('icms_data_notification');
	$notification_handler->doLoginMaintenance($user->getVar('uid'));

	$is_expired = $user->getVar('pass_expired');
	if ($is_expired == 1) {
		redirect_header(ICMS_URL . '/user.php?op=resetpass', 5, _US_PASSEXPIRED, FALSE);
	} else {
    	redirect_header($url, 1, sprintf(_US_LOGGINGU, $user->getVar('uname')), FALSE);
    }
} elseif (empty($_POST['xoops_redirect'])) {
	redirect_header(ICMS_URL . '/user.php', 5, $icmsAuth->getHtmlErrors());
} else {
	redirect_header(
		ICMS_URL . '/user.php?xoops_redirect='
		. urlencode(trim($_POST['xoops_redirect'])), 5, $icmsAuth->getHtmlErrors(), FALSE
	);
}
exit();
