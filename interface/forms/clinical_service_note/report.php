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
 
 
include_once("../../globals.php");
include_once($GLOBALS["srcdir"]."/api.inc");
function clinical_service_note_report( $pid, $encounter, $cols, $id) {
	$count = 0;
	$data = formFetch("clinical_service_note", $id);
	if ($data) {
		print "<table><tr>";
		foreach($data as $key => $value) {
			if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
				continue;
			}
			if ($value == "on") {
				$value = "yes";
			}			
			$key=ucwords(str_replace("_"," ",$key));
			print "<td><span class=bold>".xlt($key). ": </span><span class=text>".text($value)."</span></td>";
			$count++;
			if ($count == $cols) {
				$count = 0;
				print "</tr><tr>\n";
			}
		}
	}
	print "</tr></table>";
}
?> 
