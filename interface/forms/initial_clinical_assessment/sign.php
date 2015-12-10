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

require_once(dirname(__FILE__)."/model.php");


// should be First Middle Last, Job Description (username) on Date....
$userInfo = sqlFetchArray(sqlStatement("SELECT fname, mname, lname, specialty FROM users WHERE username='".$_SESSION['authUser']."'"));
$signature = htmlspecialchars("eSigned by ".$userInfo['fname']." ".$userInfo['mname']." ".$userInfo['lname'].", ".$userInfo['speciality']." (".$_SESSION['authUser'].") on " . date('r'));
sqlStatement("UPDATE form_init_clinical_assessment SET signature = '".add_escape_custom($signature)."' WHERE id = '". add_escape_custom("$id"). "'");

//$sql="UPDATE form_init_clinical_assessment SET `signature`='mysql_real_escape_string($signature)' WHERE id='$id'";

$sql = "SELECT client_name, client_number, provider, admit_date, ";
foreach ($form_fields as $field=>$label) {
	$sql .= "`".$field."`,";
}
$sql .= "`signature` FROM form_init_clinical_assessment WHERE id='".add_escape_custom("$id")."'";

$row = sqlFetchArray(sqlStatement($sql));


$text = xlt("INITIAL CLINICAL ASSESSMENT").
"\n\n".xlt('Patient Name').": ". add_escape_custom($row['client_name']).
"\n".xlt('Patient ID').": ". $row['client_number'].
"\n".xlt('Provider').": ". add_escape_custom($row['provider']).
"\n".xlt('Date').": ". add_escape_custom($row['admit_date']);

foreach ($form_fields as $field=>$label) {
	$text .= "\n\n".xlt($label).": ". add_escape_custom($row[$field]);
}

$text .= "\n\n".xlt('Signed').": ". add_escape_custom($row['signature']);


$sql="INSERT INTO `form_init_clinical_assessment_signature` (`id`, `signed_text`) VALUES ('$id', '$text')";
sqlStatement($sql);	


