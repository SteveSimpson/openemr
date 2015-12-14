<?php
/**
 *
 * Copyright (C) 2015 Steve Simpson <steve@lcsas.us> Life Center Systems and Services
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
 
  //SANITIZE ALL ESCAPES
 $sanitize_all_escapes=true;

 //STOP FAKE REGISTER GLOBALS
 $fake_register_globals=false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
require_once("$srcdir/formdata.inc.php");
require_once(dirname(__FILE__)."/model.php");

$id = 0 + (isset($_GET['id']) ? $_GET['id'] : '');

$sets = "pid = {$_SESSION["pid"]}".
  ",groupname = '" . $_SESSION["authProvider"] . 
  "',user = '" . $_SESSION["authUser"] .
  "',authorized = $userauthorized, activity=1, date = NOW()" .
  " , provider      = '" . add_escape_custom($_POST["provider"]) . 
  "',client_name    = '" . add_escape_custom($_POST["client_name"]) . 
  "',client_number  = '" . add_escape_custom($_POST["client_number"]) .
  "',admit_date     = '" . add_escape_custom($_POST["admit_date"]) . "'";
  		

foreach ($form_fields as $f=>$label) {
	if (array_key_exists($f, $_POST)) {
		$sets .= ",$f  = '" . add_escape_custom($_POST[$f]) . "'";
	}
}
  
if (empty($id)) {
  $newid = sqlInsert("INSERT INTO form_clinical_service_note SET $sets");
  addForm($encounter, "Clinical Service Note", $newid, "clinical_service_note", $pid, $userauthorized);
}
else {
  sqlStatement("UPDATE form_clinical_service_note SET $sets WHERE id = '". add_escape_custom("$id"). "'");
}
/*
[_SESSION] => Array
(
		[site_id] => default
		[language_choice] => 1
		[authUser] => admin
		[authPass] => $2a$05$hE/YCrRYa2e2lmhul.r8Z.YV60T6q517UeFIsG4FVyZ/HQzJ3uk.q
		[authGroup] => Default
		[authUserID] => 1
		[authProvider] => Default
		[authId] => 1
		[cal_ui] => 3
		[userauthorized] => 1
		[last_update] => 1390371310
		[encounter] => 4
		[pid] => 1
*/
if (array_key_exists('signForm', $_POST)) {
	include("sign.php");
}

$_SESSION["encounter"] = $encounter;
formHeader("Redirecting....");
formJump();
formFooter();
?>
