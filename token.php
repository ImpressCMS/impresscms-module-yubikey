<?php
/**
* Token page
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*
* Conducts 2-factor authentication using i) a one-time password generated by a Yubikey hardware 
* token and ii) the user's standard ImpressCMS account password. Yubikeys must be pre-registered 
* and associated with a user account to use this page.
*/

include_once 'header.php';

$xoopsOption['template_main'] = 'yubikey_token.html';
include_once ICMS_ROOT_PATH . '/header.php';

$clean_op = $dirty_op = '';
$yubikey_token_handler = icms_getModuleHandler('token', basename(dirname(__FILE__)), 'yubikey');

// Sanitise the op parameter
if (isset($_POST['op'])) $dirty_op = htmlentities(trim($_POST['op']));
if (isset($_GET['op'])) $dirty_op = htmlentities(trim($_GET['op']));

$valid_op = array('login', '');

// Check that op is a permitted (whitelisted) value
if (in_array($dirty_op, $valid_op))
{
	// We accept that op is a whitelisted value that is safe to use
	$clean_op = $dirty_op;
	
	switch($clean_op)
	{
		case "login":

			// Initialise
			$tokenObjects = array();
			$otp_authenticated = $user_details = FALSE;
			$clean_otp = $dirty_otp = $public_id = $criteria = $tokenObj = $user_details = $user_name = '';

			// Sanitise and validate one time password (alphanumeric string 44 characters in length)
			$dirty_otp = isset($_POST['yubikey_otp']) ? strip_tags(trim($_POST['yubikey_otp'])) : '';
			$dirty_otp = mysql_real_escape_string(icms_core_DataFilter::stripSlashesGPC($dirty_otp));
			if (ctype_alnum($dirty_otp) && strlen($dirty_otp) == 44)
			{
				$clean_otp = $dirty_otp;
			}
			unset($dirty_otp);

			// Check required fields have been submitted. NB: 'pass' is not used in this script, 
			// it is cleaned & handled by the included checklogin.php. Just make sure it is present.
			if ($_POST['pass'] && !empty($clean_otp))
			{
				// Extract the public_id from the Yubikey OTP
				$public_id = substr($clean_otp, 0, 12);
				
				// Check the key exists in the database and look up the associated user
				$criteria = icms_buildCriteria(array('public_id' => $public_id));
				$tokenObjects = $yubikey_token_handler->getObjects($criteria);
				if (!empty($tokenObjects))
				{
					$tokenObj = array_shift($tokenObjects);
					
					// Check the key is actually enabled
					if ($tokenObj->getVar('yubikey_enabled', 'e') == 1)
					{
						// Lookup the associated user
						$member_handler = icms::handler("icms_member");
						$user_details = $member_handler->getUser($tokenObj->uid());

						if ($user_details)
						{
							// Validate the one-time Yubikey password against Yubico's authentication server
							$otp_authenticated = $tokenObj->verify($clean_otp);
							if ($otp_authenticated)
							{
								// Set flag indicating checklogin.php is running from Yubikey page.
								// 
								// This prevents a section of code running in checklogin.php, 
								// which should only operate from the standard ICMS login page/block. 
								// The code checks if a user account requires Yubikey authentication and 
								// redirects them to the Yubikey login page if appropriate.

								$yubikey_login_page = TRUE;

								// One time password validated, now procede to validate ICMS password. 
								// 
								// NB: ICMS allows both the login_name and email address of a user to be
								// used as the login. Old user accounts may not have a login_name, as 
								// this feature was added recently. Therefore it is safer to use the 
								// email address as the login name. The login name needs to be put back 
								// into $_POST as the yubikey login page does not ask for it, but rather
								// uses the public ID embedded in the Yubikey one time password to 
								// lookup the associated user account and get the name. Putting it 
								// into the $_POST allows the standard checklogin.php script to be used
								// with minimal modification.

								$login_name = $user_details->getVar('email', 'e');
								$_POST['uname'] = $login_name;
								unset($user_details);
								include ICMS_ROOT_PATH . '/include/checklogin.php';
							}
						}
					}
				}				
			}
			
			// If any check fails, redirect back to Yubikey login page to try again
			redirect_header(ICMS_URL . '/modules/' . basename(dirname(__FILE__))
						. '/token.php', 2, _CO_YUBIKEY_LOGIN_FAILED);

			break;

		default: // Display empty login form
	}
}

$icmsTpl->assign('yubikey_show_breadcrumb', icms::$module->config['show_breadcrumb']);
$icmsTpl->assign('yubikey_module_home', yubikey_getModuleName(TRUE, TRUE));

include_once 'footer.php';