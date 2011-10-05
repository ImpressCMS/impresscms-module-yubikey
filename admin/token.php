<?php
/**
* Admin page to manage Yubikey hardware tokens
*
* List, add, edit and delete token objects
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

/**
 * Edit a Token
 *
 * @param int $token_id Tokenid to be edited
*/
function edittoken($token_id = 0)
{
	global $yubikey_token_handler, $yubikeyModule, $icmsAdminTpl;

	$tokenObj = $yubikey_token_handler->get($token_id);

	if (!$tokenObj->isNew()){
		$yubikeyModule->displayAdminMenu(0, _AM_YUBIKEY_TOKENS . " > " . _CO_ICMS_EDITING);
		$sform = $tokenObj->getForm(_AM_YUBIKEY_TOKEN_EDIT, 'addtoken');
		$sform->assign($icmsAdminTpl);

	} else {
		$yubikeyModule->displayAdminMenu(0, _AM_YUBIKEY_TOKENS . " > " . _CO_ICMS_CREATINGNEW);
		$sform = $tokenObj->getForm(_AM_YUBIKEY_TOKEN_CREATE, 'addtoken');
		$sform->assign($icmsAdminTpl);

	}
	$icmsAdminTpl->display('db:yubikey_admin_token.html');
}

include_once("admin_header.php");

$clean_op = $clean_token_id = '';
$yubikey_token_handler = icms_getModuleHandler('token');

/** Create a whitelist of valid values, be sure to use appropriate types for each value
 * Be sure to include a value for no parameter, if you have a default condition
 */
$valid_op = array ('mod','changedField','addtoken','del','changeStatus', '');

if (isset($_GET['op'])) $clean_op = htmlentities($_GET['op']);
if (isset($_POST['op'])) $clean_op = htmlentities($_POST['op']);

/** Again, use a naming convention that indicates the source of the content of the variable */
if (isset($_GET['token_id'])) $clean_token_id = isset($_GET['token_id']) ? (int) $_GET['token_id'] : 0;
if (isset($_POST['token_id'])) $clean_token_id = isset($_POST['token_id']) ? (int) $_POST['token_id'] : 0 ;

if (in_array($clean_op,$valid_op,true)){
  switch ($clean_op) {
  	case "mod":
  	case "changedField":

  		icms_cp_header();
  		edittoken($clean_token_id);
  		break;

  	case "addtoken":
          include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
          $controller = new IcmsPersistableController($yubikey_token_handler);
   		  $controller->storeFromDefaultForm(_AM_YUBIKEY_TOKEN_CREATED, _AM_YUBIKEY_TOKEN_MODIFIED);

  		break;

	case "changeStatus":
		$status = $ret = '';
		$status = $yubikey_token_handler->changeStatus($clean_token_id, 'yubikey_enabled');
		$ret = '/modules/' . basename(dirname(dirname(__FILE__))) . '/admin/token.php';
		if ($status == 0) {
			redirect_header(ICMS_URL . $ret, 2, _AM_YUBIKEY_TOKEN_DISABLED);
		} else {
			redirect_header(ICMS_URL . $ret, 2, _AM_YUBIKEY_TOKEN_ENABLED);
		}
		
		break;

  	case "del":
  	    include_once ICMS_ROOT_PATH."/kernel/icmspersistablecontroller.php";
          $controller = new IcmsPersistableController($yubikey_token_handler);
  		$controller->handleObjectDeletion();

  		break;

  	default:

  		icms_cp_header();

  		$icmsModule->displayAdminMenu(0, _AM_YUBIKEY_TOKENS);

		// if no op is set, but there is a (valid) rights_id, display a single object
		if ($clean_token_id) {
			$tokenObj = $yubikey_token_handler->get($clean_token_id);
			if ($tokenObj->id()) {
				$tokenObj->displaySingleObject();
			}
		}

  		include_once ICMS_ROOT_PATH."/kernel/icmspersistabletable.php";
  		$objectTable = new IcmsPersistableTable($yubikey_token_handler);
		$objectTable->addQuickSearch('user_id', _AM_YUBIKEY_QUICK_SEARCH_UID);
		$objectTable->addColumn(new IcmsPersistableColumn('user_id', _GLOBAL_LEFT, true, false,
				false, _CO_YUBIKEY_TOKEN_USER_ID));
		$objectTable->addColumn(new IcmsPersistableColumn('user_id', _GLOBAL_LEFT, true, 'uid',
				false, _CO_YUBIKEY_TOKEN_UID));
		$objectTable->addColumn(new IcmsPersistableColumn('public_id', _GLOBAL_LEFT, true));
		$objectTable->addColumn(new IcmsPersistableColumn('yubikey_enabled', _GLOBAL_LEFT, true));
  		$objectTable->addIntroButton('addtoken', 'token.php?op=mod', _AM_YUBIKEY_TOKEN_CREATE);
  		$icmsAdminTpl->assign('yubikey_token_table', $objectTable->fetch());
  		$icmsAdminTpl->display('db:yubikey_admin_token.html');
  		break;
  }
  icms_cp_footer();
}
/**
 * If you want to have a specific action taken because the user input was invalid,
 * place it at this point. Otherwise, a blank page will be displayed
 */