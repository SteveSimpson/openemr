<?php
/**
 *
 * Copyright (C) 2015 Steve Simpson <steve@lcsas.us> Life Center Systems and Software
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
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
$obj = $formid ? formFetch("form_init_clinical_assessment", $formid) : array();

if (count($obj) == 0) {
	$obj['admit_date'] = date('Y-m-d');
}

// Get the providers list.
$users = sqlStatement("SELECT id, username, fname, lname FROM users WHERE " .
  "authorized != 0 AND active = 1 ORDER BY lname, fname");
 
function writeRow($label, $field, $disabled=false) {
	global $obj;
	global $form_headers;
	if (array_key_exists($field, $form_headers)) {
		echo "<tr><th align=\"left\" colspan='4'>".xlt($form_headers[$field])."</th></tr>\n";
	}
	echo "<tr><td align=\"left\" class=\"forms\">&nbsp;&nbsp;".xlt($label)."</td>";
 	echo "<td colspan=\"3\"><textarea name=\"$field\" rows=\"2\" cols=\"60\" wrap=\"virtual name\" ".($disabled ? 'disabled="disabled"' : '').">";
 	if (isset($obj{$field})) {
 		echo text($obj{$field});
 	}
 	echo "</textarea></td></tr>\n";
}

?>
<html>
<head>
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

	<form method='post' name='my_form'
		<?php echo "action='$rootdir/forms/init_clinical_assessment/save.php?id=" . attr($formid) ."'"; ?>>

		<table border="0">
			<tr>
				<th colspan='4'><?php echo xlt('Initial Clinical Assessment') ?></th>
			</tr>
			<tr>
				<td align="left" class="forms" class="forms"><?php echo xlt('Patient Name' ); ?></td>
				<td class="forms"><label class="forms-data"> <?php if (is_numeric($pid)) {
    
   $result = getPatientData($pid, "fname,lname,squad");
   echo text($result['fname'])." ".text($result['lname']);}
   $patient_name=($result['fname'])." ".($result['lname']);
   ?>
				</label> <input type="hidden" name="client_name"
					value="<?php echo attr($patient_name);?>"></td>
				<td align="left" class="forms"><?php echo xlt('DOB'); ?></td>
				<td class="forms"><label class="forms-data"> <?php if (is_numeric($pid)) {
    
   $result = getPatientData($pid, "*");
   echo text($result['DOB']);}
   $dob=($result['DOB']);
   ?>
				</label> <input type="hidden" name="DOB" value="<?php echo attr($dob);?>">
				</td>
			</tr>
			<tr>



				<td align="left" class="forms"><?php echo xlt('Patient ID'); ?></td>
				<td class="forms"><label class="forms-data"> <?php if (is_numeric($pid)) {
    
   $result = getPatientData($pid, "*");
   echo text($result['pid']);}
   $patient_id=$result['pid'];
   ?>
				</label> <input type="hidden" name="client_number"
					value="<?php echo attr($patient_id);?>"></td>


				<td align="left" class="forms"><?php echo xlt('Date of Service'); ?></td>
				<td class="forms"><input type='text' size='10'
					name='admit_date' id='admission_date'
					<?php if (isset($disabled)) echo attr($disabled); ?>
					value='<?php if (isset($obj{"admit_date"})) echo attr($obj{"admit_date"}); ?>'
					title='<?php echo xla('yyyy-mm-dd Date of service'); ?>'
					onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' /> <img
					src='../../pic/show_calendar.gif' align='absbottom' width='24'
					height='22' id='img_admission_date' border='0' alt='[?]'
					style='cursor: pointer; cursor: hand'
					title='<?php echo xla('Click here to choose a date'); ?>'></td>

			</tr>
			<tr>
				<td align="left" class="forms"><?php echo xlt('Provider'); ?></td>
				<td class="forms" width="280px"><?php

    echo "<select name='provider' style='width:60%' />";
    while ($urow = sqlFetchArray($users)) {
      echo "    <option value='" . attr($urow['lname']) . "'";
      if (isset($obj{"provider"}) && $urow['lname'] == attr($obj{"provider"})) echo " selected";
      echo ">" . text($urow['lname']);
      if ($urow['fname']) echo ", " . text($urow['fname']);
      echo "</option>\n";
    }
    echo "</select>";
?>
				</td>
	<tr>
	
  <td colspan='3' nowrap style='font-size:8pt'>
   &nbsp;
	</td>
	</tr>
<?php 
	foreach($form_fields as $field=>$label) {
		writeRow($label,$field);
	}
?>
	<tr>
		<td align="left" colspan="3" style="padding-bottom:7px;"></td>
	</tr>
	<tr>
		<td align="left" colspan="3" style="padding-bottom:7px;"></td>
	</tr>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' value='<?php echo xlt('Save');?>'
					class="button-css">&nbsp; <input type='button'
					value="Print" onclick="window.print()" class="button-css">&nbsp;
					<input type='button' class="button-css"
					value='<?php echo xlt('Cancel');?>'
					onclick="top.restoreSession();location='<?php echo "$rootdir/patient_file/encounter/$returnurl" ?>'" /></td>
			</tr>
		</table>
	</form>

	<script>
/* required for popup calendar */
Calendar.setup({inputField:"admission_date", ifFormat:"%Y-%m-%d", button:"img_admission_date"});

</script>
	<?php
formFooter();
?>