<?php
/**
* Footer page included at the end of each page on user side of the mdoule
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

$icmsTpl->assign("yubikey_adminpage", yubikey_getModuleAdminLink());
$icmsTpl->assign("yubikey_is_admin", $yubikey_isAdmin);
$icmsTpl->assign('yubikey_url', YUBIKEY_URL);
$icmsTpl->assign('yubikey_images_url', YUBIKEY_IMAGES_URL);

$xoTheme->addStylesheet(YUBIKEY_URL . 'module' 
		. (( defined("_ADM_USE_RTL") && _ADM_USE_RTL )?'_rtl':'').'.css');

include_once(ICMS_ROOT_PATH . '/footer.php');