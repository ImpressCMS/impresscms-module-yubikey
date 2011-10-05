<?php

/**
* Classes responsible for managing Yubikey token objects
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2011
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Tom Corwine (yubico@corwine.org)
* @package		yubikey
* @version		$Id$
*/

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// including the IcmsPersistabelSeoObject
include_once ICMS_ROOT_PATH . '/kernel/icmspersistableobject.php';
include_once(ICMS_ROOT_PATH . '/modules/yubikey/include/functions.php');

class YubikeyToken extends IcmsPersistableObject {
	
		// Input
		private $_id;
		private $_signatureKey;

		// Output
		private $_response;

		// Internal
		private $_curlResult;
		private $_curlError;
		private $_timestampTolerance;
		private $_curlTimeout;

	/**
	 * Constructor
	 *
	 * @param object $handler YubikeyPostHandler object
	 */
	public function __construct(& $handler) {
		global $icmsConfig, $icmsUser, $yubikeyConfig;

		$this->IcmsPersistableObject($handler);

		$this->quickInitVar('token_id', XOBJ_DTYPE_INT, true);
		$this->quickInitVar('user_id', XOBJ_DTYPE_INT, true);
   		$this->quickInitVar('public_id', XOBJ_DTYPE_TXTBOX, true);
		$this->quickInitVar('yubikey_enabled', XOBJ_DTYPE_INT, true, false, false, 1);

		$this->setControl('user_id', 'user');
		$this->setControl('yubikey_enabled', 'yesno');

		// load Yubico Client ID and signature key

		if (is_int ($yubikeyConfig['yubikey_client_id']) &&
			$yubikeyConfig['yubikey_client_id'] > 0)
			$this->_id = $yubikeyConfig['yubikey_client_id'];

		if (strlen ($yubikeyConfig['yubikey_api_key']) == 28)
		{
			$this->_signatureKey = base64_decode ($yubikeyConfig['yubikey_api_key']);
		}

		// Set defaults
		$this->_timestampTolerance = $yubikeyConfig['yubikey_timestamp_tolerance'];
		$this->_curlTimeout = $yubikeyConfig['yubikey_curl_timeout'];
	}

	/**
	 * Overriding the IcmsPersistableObject::getVar method to assign a custom method on some
	 * specific fields to handle the value before returning it
	 *
	 * @param str $key key of the field
	 * @param str $format format that is requested
	 * @return mixed value of the field that is requested
	 */
	function getVar($key, $format = 's') {
		if ($format == 's' && in_array($key, array ('user_id', 'yubikey_enabled'))) {
			return call_user_func(array ($this,	$key));
		}
		return parent :: getVar($key, $format);
	}

	// looks up the user name and returns a link
	public function user_id()
	{
		return yubikey_getLinkedUnameFromId($this->getVar('user_id', 'e'));
	}

	// converts the yubikey_enabled field to human readable
	public function yubikey_enabled()
	{
		$status = $button = '';

		$status = $this->getVar('yubikey_enabled', 'e');
		$button = '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/token.php?token_id=' . $this->getVar('token_id')
				. '&amp;op=changeStatus">';
		if ($status == false) {
			$button .= '<img src="../images/button_cancel.png" alt="' . _CO_YUBIKEY_TOKEN_ENABLED
				. '" title="' . _CO_YUBIKEY_TOKEN_DISABLE . '" /></a>';

		} else {

			$button .= '<img src="../images/button_ok.png" alt="' . _CO_YUBIKEY_TOKEN_DISABLED
				. '" title="' . _CO_YUBIKEY_TOKEN_ENABLE . '" /></a>';
		}
		return $button;
	}

	public function uid()
	{
		return $this->getVar('user_id', 'e');
	}

	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	//	Yubikey API methods, by Tom Corwine (yubico@corwine.org)
	//	
	//	verify(string) - Accepts otp from Yubikey. Returns TRUE for authentication success, otherwise FALSE.
	//	getLastResponse() - Returns response message from verification attempt.
	//	getTimestampTolerance() - Gets the tolerance (+/-, in seconds) for timestamp verification
	//	setTimestampTolerance(int) - Sets the tolerance (in seconds, 0-86400) - default 600 (10 minutes).
	//		Returns TRUE on success and FALSE on failure.
	//	getCurlTimeout() - Gets the timeout (in seconds) CURL uses before giving up on contacting Yubico's server.
	//	setCurlTimeout(int) - Sets the CURL timeout (in seconds, 0-600, 0 means indefinitely) - default 10.
	//		Returns TRUE on success and FALSE on failure.
	//////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////

	public function getTimestampTolerance()
	{
		return $this->_timestampTolerance;
	}

	public function setTimestampTolerance($int)
	{
		if ($int > 0 && $int < 86400)
		{
			$this->_timestampTolerance = $int;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getCurlTimeout()
	{
		return $this->_curlTimeout;
	}

	public function setCurlTimeout($int)
	{
		if ($int > 0 && $int < 600)
		{
			$this->_curlTimeout = $int;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getLastResponse()
	{
		return $this->_response;
	}

	public function verify($otp)
	{
		unset ($this->_response);
		unset ($this->_curlResult);
		unset ($this->_curlError);

		$otp = strtolower ($otp);

		if (!$this->_id)
		{
			$this->_response = "ID NOT SET";
			return false;
		}

		if (!$this->otpIsProperLength($otp))
		{
			$this->_response = "BAD OTP LENGTH";
			return false;
		}

		if (!$this->otpIsModhex($otp))
		{
			$this->_response = "OTP NOT MODHEX";
			return false;
		}

		$urlParams = "id=".$this->_id."&otp=".$otp;

		$url = $this->createSignedRequest($urlParams);

		if ($this->curlRequest($url)) //Returns 0 on success
		{
			$this->_response = "ERROR CONNECTING TO YUBICO - ".$this->_curlError;
			return false;
		}

		foreach ($this->_curlResult as $param)
		{
			if (substr ($param, 0, 2) == "h=") $signature = substr (trim ($param), 2);
			if (substr ($param, 0, 2) == "t=") $timestamp = substr (trim ($param), 2);
			if (substr ($param, 0, 7) == "status=") $status = substr (trim ($param), 7);
		}

		// Concatenate string for signature verification
		$signedMessage = "status=".$status."&t=".$timestamp;

		if (!$this->resultSignatureIsGood($signedMessage, $signature))
		{
			$this->_response = "BAD RESPONSE SIGNATURE";
			return false;
		}

		if (!$this->resultTimestampIsGood($timestamp))
		{
			$this->_response = "BAD TIMESTAMP";
			return false;
		}

		if ($status != "OK")
		{
			$this->_response = $status;
			return false;
		}

		// Everything went well - We pass
		$this->_response = "OK";
		return true;
	}

	protected function createSignedRequest($urlParams)
	{
		if ($this->_signatureKey)
		{
			$hash = urlencode (base64_encode (hash_hmac ("sha1", $urlParams, $this->_signatureKey,
					true)));
			return "https://api.yubico.com/wsapi/verify?".$urlParams."&h=".$hash;
		}
		else
		{
			return "https://api.yubico.com/wsapi/verify?".$urlParams;
		}
	}

	protected function curlRequest($url)
	{
		$ch = curl_init ($url);

		curl_setopt ($ch, CURLOPT_TIMEOUT, $this->_curlTimeout);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->_curlTimeout);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, true);

		$this->_curlResult = explode ("\n", curl_exec($ch));

		$this->_curlError = curl_error ($ch);
		$error = curl_errno ($ch);

		curl_close ($ch);

		return $error;
	}

	protected function otpIsProperLength($otp)
	{
		if (strlen ($otp) == 44)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function otpIsModhex($otp)
	{
		$modhexChars = array ("c","b","d","e","f","g","h","i","j","k","l","n","r","t","u","v");

		foreach (str_split ($otp) as $char)
		{
			if (!in_array ($char, $modhexChars)) return false;
		}

		return true;
	}

	protected function resultTimestampIsGood($timestamp)
	{
		// Turn times into 'seconds since Unix Epoch' for easy comparison
		$now = date ("U");
		$timestampSeconds = (date_format (date_create (substr ($timestamp, 0, -4)), "U"));

		// If date() functions above fail for any reason, so do we
		if (!$timestamp || !$now) return false;

		if (($timestampSeconds + $this->_timestampTolerance) > $now &&
		    ($timestampSeconds - $this->_timestampTolerance) < $now)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function resultSignatureIsGood($signedMessage, $signature)
	{
		if (!$this->_signatureKey) return true;

		if (base64_encode (hash_hmac ("sha1", $signedMessage, $this->_signatureKey, true))
				== $signature)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	///////////////////////////////////////////////////////////////////////
	///// END Yubikey API methods by Tom Corwine (yubico@corwine.org) /////
	///////////////////////////////////////////////////////////////////////
}
class YubikeyTokenHandler extends IcmsPersistableObjectHandler {

	/**
	 * Constructor
	 */
	public function __construct(& $db) {
		$this->IcmsPersistableObjectHandler($db, 'token', 'token_id', 'public_id', 'public_id',
				'yubikey');
	}

	/**
	 * Toggles Yubikey authentication online or offline
	 *
	 * @param int $token_id
	 * @param str $field
	 * @return int $visibility
	 */
	public function changeStatus($token_id, $field) {

		$visibility = $tokenObj = '';

		$tokenObj = $this->get($token_id);
		if ($tokenObj->getVar($field, 'e') == true) {
			$tokenObj->setVar($field, 0);
			$visibility = 0;
		} else {
			$tokenObj->setVar($field, 1);
			$visibility = 1;
		}
		$this->insert($tokenObj, true);

		return $visibility;
	}

	protected function beforeSave(& $tokenObj)
	{
		$one_time_password = $yubikey_user = '';
		$valid_key = false;
		$yubikey_token_handler = icms_getModuleHandler('token', basename(dirname(dirname(__FILE__))), 
				'yubikey');
		
		$user_id = $tokenObj->getVar('user_id');
		$one_time_password = $tokenObj->getVar('public_id');
		$public_id_length = strlen($tokenObj->getVar('public_id'));
		
		// check the length of the public_id field
		switch ($public_id_length)
		{
			case "12": // only the public ID of the key was submitted
				$valid_key = ctype_alnum($one_time_password) ? true : false;
				break;
			
			case "44": // public ID + one time password submitted, validate the key against Yubico
				$valid_key = ctype_alnum($one_time_password) ? true : false;
				if ($valid_key)
				{
					$valid_key = $tokenObj->verify($one_time_password);
					$tokenObj->setVar('public_id', substr($tokenObj->getVar('public_id'), 0,12));
				}
				break;
			
			default: // if some other length was entered, it's wrong
				$valid_key = false;
				break;
		}

		// check for duplicate Yubikeys - each may only be assigned to one account
		if ($valid_key)
		{
			$criteria = icms_buildCriteria(array('public_id' => $tokenObj->getVar('public_id')));
			$duplicate_keys = $yubikey_token_handler->getObjects($criteria);
			$duplicate = array_shift($duplicate_keys);
			if (!empty($duplicate))
			{
				if ($tokenObj->getVar('token_id') !== $duplicate->getVar('token_id'))
				{
					$tokenObj->setErrors(_CO_YUBIKEY_ERROR_DUPLICATE_KEYS_NOT_ALLOWED);
					$valid_key = false;
				}
			}
		}
		
		// check that the designated user exists
		$member_handler = &xoops_gethandler('member');
		$yubikey_user = $member_handler->getUser($user_id);
		if (!empty($yubikey_user))
		{
			$valid_key = false;
		}
		
		return $valid_key;
	}
}