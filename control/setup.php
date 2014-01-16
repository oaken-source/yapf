<?php require_once($_SERVER['DOCUMENT_ROOT']."/control/valid_request.php");

/******************************************************************************
 *                           knights of kalindor                              *
 *                                                                            *
 *    Copyright (C) 2013  Karl Kronberger, Andreas Grapentin                  *
 *                                                                            *
 *    This program is free software: you can redistribute it and/or modify    *
 *    it under the terms of the GNU General Public License as published by    *
 *    the Free Software Foundation, either version 3 of the License, or       *
 *    (at your option) any later version.                                     *
 *                                                                            *
 *    This program is distributed in the hope that it will be useful,         *
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of          *
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           *
 *    GNU General Public License for more details.                            *
 *                                                                            *
 *    You should have received a copy of the GNU General Public License       *
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.   *
 ******************************************************************************/

define("SCRIPT_START", microtime(true));

date_default_timezone_set("Europe/Berlin");

// auxiliary function definitions
require_once("control/util.php");

// setup analytics wrapper
require_once("control/analytics.php");

// connect to databases
require_once("control/db/logdb.php");
require_once("control/db/gamedb.php");

// upgrade databases, if requested and necessary
if (isset($_GET['evolve_dbs']))
  require_once("control/db/evolve.php");

// initialize session (TODO)
require_once("control/session.php");

// initialize mail handler
require_once("control/mail.php");

// setup html template and renderer class
require_once("control/template.php");
require_once("control/renderer.php");

?>
