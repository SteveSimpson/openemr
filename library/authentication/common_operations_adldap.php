<?php
/**
 * This is a library of commonly used functions for managing data for authentication
 * for LDAP. 
 * 
 * Based on work by Kevin Yeh.
 * 
 * Copyright (C) 2015 Steve Simpson <steve@lcsas.us> and OEMR <www.oemr.org>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Steve Simpson <steve@lcsas.us>
 * @link    http://www.open-emr.org
 */

require_once("$srcdir/authentication/privDB.php");
define("TBL_USERS_SECURE","users_secure");
define("TBL_USERS","users");

define("COL_PWD","password");
define("COL_UNM","username");
define("COL_ID","id");
define("COL_SALT","salt");
define("COL_LU","last_update");
define("COL_PWD_H1","password_history1");
define("COL_SALT_H1","salt_history1");
define("COL_ACTIVE","active");

define("COL_PWD_H2","password_history2");
define("COL_SALT_H2","salt_history2");


/**
 * We are using LDAP. Set that in the DB if this function is called.
 *
 *
 * @param type $username
 * @param type $userid
 */
function purgeCompatabilityPassword($username,$userid)
{
	$purgeSQL = " UPDATE " . TBL_USERS
	." SET ". COL_PWD . "='**Using adLDAP**' "
			." WHERE ".COL_UNM. "=? "
					." AND ".COL_ID. "=?";
	privStatement($purgeSQL,array($username,$userid));
}


/**
 * create a new password entry in the users_secure table
 *
 * @param type $username
 * @param type $password  Passing by reference so additional copy is not created in memory
 */
function initializePassword($username,$userid,&$password)
{
	//TODO: Need to add password functions for LDAP if we want to be able to create / update LDAP Passwords.
	return '';
}


/**
 *
 * @param type $username
 * @param type $password
 * @return boolean  returns true if the password for the given user is correct, false otherwise.
 */
function confirm_user_password($username, &$password)
{
	global $adldap_options;
	
	$adldap = new \Adldap\Adldap($adldap_options);
	
	if (!isset($username || empty($username) || trim($username) == '') {
		return false;
	}
	
	$ret = $adldap->authenticate($username, $password);

	if ($ret === true) {
		return true;
	} else {
		return false;
	}
}
