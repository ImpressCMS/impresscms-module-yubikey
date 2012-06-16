<?php
/**
* User index page of the module
*
* Including the token page
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		yubikey
* @version		$Id$
*/

include_once "../../mainfile.php";  
include_once ICMS_ROOT_PATH . "/header.php"; 
header('location: token.php');
exit;