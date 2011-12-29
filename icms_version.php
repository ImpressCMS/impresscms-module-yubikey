<?php
/**
* Yubikey version infomation
*
* This file holds the configuration information of this module
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**  General Information  */
$modversion = array(
  'name'=> _MI_YUBIKEY_MD_NAME,
  'version'=> 1.1,
  'description'=> _MI_YUBIKEY_MD_DESC,
  'author'=> "Madfish (Simon Wilkinson)",
  'credits'=> "Tom Corwine developed the Yubikey PHP API calls used in this module.",
  'help'=> "",
  'license'=> "GNU General Public License (GPL)",
  'official'=> 0,
  'dirname'=> basename( dirname( __FILE__ ) ),

/**  Images information  */
  'iconsmall'=> "images/icon_small.png",
  'iconbig'=> "images/icon_big.png",
  'image'=> "images/icon_big.png", /* for backward compatibility */

/**  Development information */
  'status_version'=> "1.1",
  'status'=> "Beta",
  'date'=> "29/12/2011",
  'author_word'=> "This module protects designated accounts in the events their password is compromised. It is not a substitute for sensible password management.",

/** Contributors */
  'developer_website_url' => "https://www.isengard.biz",
  'developer_website_name' => "Isengard.biz",
  'developer_email' => "simon@isengard.biz");

$modversion['people']['developers'][] = "Madfish (Simon Wilkinson)";

/** Manual */
$modversion['manual']['wiki'][] = "<a href='http://wiki.impresscms.org/index.php?title=Yubikey' target='_blank'>English</a>";

$modversion['warning'] = _CO_ICMS_WARNING_BETA;

/** Administrative information */
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

/** Database information */
$modversion['object_items'][1] = 'token';

$modversion["tables"] = icms_getTablesArray($modversion['dirname'], $modversion['object_items']);

/** Install and update informations */
$modversion['onInstall'] = "include/onupdate.inc.php";
$modversion['onUpdate'] = "include/onupdate.inc.php";

/** Search information */
$modversion['hasSearch'] = 0;

/** Menu information */
$modversion['hasMain'] = 1;

$modversion['blocks'][1] = array(
  'file' => 'yubikey_login.php',
  'name' => _MI_YUBIKEY_LOGIN,
  'description' => _MI_YUBIKEY_LOGINDSC,
  'show_func' => 'yubikey_login_show',
  'edit_func' => 'yubikey_login_edit',
  'options' => '1',
  'template' => 'yubikey_login.html');

/** Templates information */
$modversion['templates'][1] = array(
  'file' => 'yubikey_header.html',
  'description' => 'Module Header');

$modversion['templates'][] = array(
  'file' => 'yubikey_footer.html',
  'description' => 'Module Footer');

$modversion['templates'][]= array(
  'file' => 'yubikey_admin_token.html',
  'description' => 'token Admin Index');

$modversion['templates'][]= array(
  'file' => 'yubikey_requirements.html',
  'description' => 'token Admin Index');

$modversion['templates'][]= array(
  'file' => 'yubikey_token.html',
  'description' => 'token Index');

/** Preferences information */

// Yubikey Client ID - you MUST get one of these from: https://upgrade.yubico.com/getapikey/
$modversion['config'][1] = array(
	'name' => 'yubikey_client_id',
	'title' => '_MI_YUBIKEY_CLIENT_ID',
	'description'=> '_MI_YUBIKEY_CLIENT_IDDSC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => ''
);

// Yubikey API key - you MUST get one of these from: https://upgrade.yubico.com/getapikey/
$modversion['config'][] = array(
	'name' => 'yubikey_api_key',
	'title' => '_MI_YUBIKEY_API_KEY',
	'description' => '_MI_YUBIKEY_API_KEYDSC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => ''
);

$modversion['config'][] = array(
	'name' => 'yubikey_timestamp_tolerance',
	'title' => '_MI_YUBIKEY_TIMESTAMP_TOLERANCE',
	'description' => '_MI_YUBIKEY_TIMESTAMP_TOLERANCEDSC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => '600'
);

$modversion['config'][] = array(
	'name' => 'yubikey_curl_timeout',
	'title' => '_MI_YUBIKEY_CURL_TIMEOUT',
	'description' => '_MI_YUBIKEY_CURL_TIMEOUTDSC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => '10'
);

$modversion['config'][] = array(
	'name' => 'show_breadcrumb',
	'title' => '_MI_YUBIKEY_SHOW_BREADCRUMB',
	'description' => '_MI_YUBIKEY_SHOW_BREADCRUMB_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '1');

/** Comments information */
$modversion['hasComments'] = 0;