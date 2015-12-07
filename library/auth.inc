<?php
/**
 * Authorization functions wrapper.
 * 
 * This script calls the authentication package based on the what is configured
 * for the site.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
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

require_once("{$GLOBALS['srcdir']}/log.inc");
require_once("{$GLOBALS['srcdir']}/sql.inc");
require_once("{$GLOBALS['srcdir']}/acl.inc");

// default to basic authentication
if (! isset($GLOBALS['oer_config']['authProvider'])) {
	$GLOBALS['oer_config']['authProvider'] = 'basic';
}

require_once("{$GLOBALS['srcdir']}/authentication/auth_" . $GLOBALS['oer_config']['authProvider'] . ".inc" );