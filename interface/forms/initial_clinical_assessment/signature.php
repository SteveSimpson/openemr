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
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once(dirname(__FILE__)."/model.php");
formHeader("Form:Initial Clinical Assessment");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';

?>
<html><head>
<?php html_header_show();?>
<script type="text/javascript" src="../../../library/dialog.js"></script>
<!-- pop up calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
</head>
<body class="body_top">
<div>
<?php

if (isset($_GET['id']) && isset($_GET['ts'])) {
	$sql = "SELECT `signed_text` FROM `form_init_clinical_assessment_signature` WHERE id='".add_escape_custom($_GET['id'])."'";
	$sql .= " AND `timestamp`='".add_escape_custom(urldecode($_GET['ts']))."'";
	
	$query = sqlStatement($sql);

	if ($row = sqlFetchArray($query)) {
		$lines=explode("\n", $row['signed_text']);
		foreach ($lines as $line) {
			echo "$line<br/>\n";
		}
	} else {
		echo "Unable to find signature.\n";
	}
} else {
	echo "Invalid search.\n";
}
?>
<br/><hr/><br/>
</div>
<input type='button'  value="Print" onclick="window.print()" class="button-css">&nbsp;
<input type='button' class="button-css" value='<?php echo xlt('Cancel');?>'
 onclick="top.restoreSession();location='<?php echo "$rootdir/patient_file/encounter/$returnurl" ?>'" />
<?php
formFooter();
?>

