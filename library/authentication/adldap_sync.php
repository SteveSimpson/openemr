#!/usr/bin/env php
<?php
/**
 * Synchronize users with the Active Directory
 * - read user names and info from Active Directory
 * - update the Users table in OpenEMR
 * - handles deleted usernames
 * - handles new usernames
 * 
 * 12 Dec 2007 - Jason Morrill
 *  5 Dec 2015 - Steve Simpson
 **/

//TODO: This should loop through all directories under sites, not just work with default

$srcdir = dirname(dirname(dirname(__FILE__))); 

require_once($srcdir . "/library/vendor/autoload.php");
require_once($srcdir . "/sites/default/adldap_conf.php");
require_once($srcdir . "/library/sql.inc");

/*====================================================
 Usernames to ignore when querying Active Directory
** CHANGE THIS ** to accommodate your AD userbase
* but use the conf file.
*====================================================*/

$excludedUsers = array ("Administrator", "SQLServer", "SQLDebugger",
		"TsInternetUser", "someotheruser",
);
// this should come from config
if (isset($adldap_excluded_users)) {
	$excludedUsers = $adldap_excluded_users;
}


/*====================================================
 * No changes below here should be necessary
*===================================================*/


// the attributes we pull from Active Directory
$ldapAttributes = array("givenname", "sn", "displayname",
		"physicaldeliveryofficename", "homephone",
		"telephonenumber", "mobile", "pager",
		"facsimiletelephonenumber", "mail", "title",
		"department", "streetaddress", "postofficebox",
		"l", "st", "postalcode",
);

// mapping of Active Directory attributes to OpenEMR Users table columns
$attributeMapping = array (
		"givenname" => "fname"
		,"sn" => "lname"
		//,"displayname" => ""
		//,"physicaldeliveryofficename" => ""
		//,"homephone"  => ""
		,"telephonenumber" => "phonew1"
		,"mobile" => "phonecell"
		//,"pager" => ""
		,"facsimiletelephonenumber" => "fax"
		,"mail" => "email"
		,"title" => "specialty"
		//,"department" => ""
		,"streetaddress" => "street"
		,"postofficebox" => "streetb"
		,"l" => "city"
		,"st" => "state"
		,"postalcode" => "zip"
);

// create new instance and connect to AD with user & pass
// defined in adLDAP_conf.inc
$adldap = new \Adldap\Adldap($adldap_options);
$adUsers = $adldap->users()->all(); 

// gather all our known usernames from OpenEMR
// they will be used to compare what is found in Active Directory
$oemrUsers = array();
$sqlH = sqlStatement("select id, username from users", []);
while ($onerow = sqlFetchArray($sqlH)) { array_push($oemrUsers, $onerow); }

$activeAdUsers=[];
$adUserCount = 0;

//failsafe
if (!$adUsers) {
	echo "Error getting users from AD\n";
	die();
}
foreach ($adUsers as $userInfo) {
	// loop over all the Active Directory users
	
	$adUser = $userInfo->getAttribute('samaccountname')[0];
	$skip = 0;
	
	foreach ($excludedUsers as $ex) {
		if ($ex == $adUser) { $skip = 1; break; }
	}
	//add code to require a specific group
	if (isset($adldap_require_group) 
       && $userInfo->inGroup($adldap_require_group) == false) {
		$skip = 1;
	}
	if ($userInfo->isActive() == false) {
		$skip = 1;
	}
	if ($skip == 1) { continue; }
	
	$activeAdUsers[]=$adUser;

	if (NewUser($adUser, $oemrUsers)) {
		// add new user
		echo "Adding user $adUser";
		if (AddUser($userInfo)) { 
			echo ", OK\n";
			$adUserCount++;
		} else { 
			echo ", FAILED\n"; 
		}
	} else {
		// update existing users with Active Directory info
		echo "existing user $adUser";
		if (UpdateUser($userInfo)) { 
			echo ", OK\n"; 
			$adUserCount++;
		} else { 
			echo ", FAILED\n"; 
		}
	}
}

// re-query in case we have updated a username in the previous loop
$oemrUsers = array();
$sqlH = sqlStatement("select id, username from users");
while ($onerow = sqlFetchArray($sqlH)) { array_push($oemrUsers, $onerow); }

// for all the usernames in OpenEMR and NOT IN Active Directory
// de-activate them in OpenEMR
foreach ($oemrUsers as $user) {
	$found = false;
	foreach ($activeAdUsers as $adUser) {
		if ($user['username'] == $adUser) { 
			$found = true; 
			break; 
		}
	}
	// only deactivate if we actually have users
	if ($adUserCount && $found == false) {
		$sqlstmt = "update users set active=0 where ".
				"id=".$user['id'];
		if (sqlStatement($sqlstmt)) { 
			echo "Deactivated ".$user['username']." from OpenEMR\n"; 
		} else { 
			echo "Failed to deactivate ".$user['username']." from OpenEMR\n"; 
		}
	}
}
	
exit;
	
	
/*=====================================
  Add a user to the OpenEMR database
  =====================================*/
function AddUser($userInfo) {
	global $attributeMapping;
	global $GLOBALS;

	$adUsername = $GLOBALS['adodb']['db']->qstr($userInfo->getAttribute('samaccountname')[0]);

	ksort($attributeMapping);
	$sqlstmt = "insert into users (id, username";
	foreach ($attributeMapping as $key=>$value) {
		$sqlstmt .= ", ".$value;
	}
	$sqlstmt .= ") values (null, ". $adUsername;
	foreach ($attributeMapping as $key=>$value) {
		$sqlstmt .= ", ".$GLOBALS['adodb']['db']->qstr($userInfo->getAttribute($key)[0]);
	}
	$sqlstmt .= ")";
	if (sqlStatement($sqlstmt) == false) { return false; }

	// add the user to the default group
	$sqlstmt = "insert into groups (".
			"name, user ".
			") values (".
			"'Default'".
			", ".$adUsername.
			")";
	if (sqlStatement($sqlstmt) == false) { return false; }
	return true;
}


/*=====================================
  Update and existing user in the OpenEMR database
  =====================================*/
function UpdateUser($userInfo) {
	global $attributeMapping;
	global $GLOBALS;

	$adUsername = $GLOBALS['adodb']['db']->qstr($userInfo->getAttribute('samaccountname')[0]);

	ksort($attributeMapping);

	$sqlstmt = "update users set active=1, ";
	$comma = "";
	foreach ($attributeMapping as $key=>$value) {
		$sqlstmt .= $comma . $value . "=". $GLOBALS['adodb']['db']->qstr($userInfo->getAttribute($key)[0]);
		$comma = ", ";
	}
	$sqlstmt .= " where username = ". $adUsername;

	return sqlStatement($sqlstmt);
}

/*=====================================
  Determine if the supplied username
  exists in the OpenEMR Users table
  =====================================*/
function NewUser($username, $oemrUsers) {
	foreach ($oemrUsers as $user) {
		if ($user['username'] == $username) { return false; }
	}
	return true;
}
