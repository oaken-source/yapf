<?php require_once($_SERVER['DOCUMENT_ROOT']."/yapf/valid_request.php");

/******************************************************************************
 *                                    yapf                                    *
 *                                                                            *
 *    Copyright (C) 2013 - 2014  Karl Kronberger, Andreas Grapentin           *
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

// preparation
define('SCRIPT_START', microtime(true));
error_reporting(E_ALL | E_STRICT);

// get some basic utility methods
require_once("yapf/util/redirect.php");
require_once("yapf/util/assert.php");
require_once("yapf/util/require.php");
require_once("yapf/util/crypto.php");

// load settings
require_once("yapf/ini.php");
require_once("yapf/config.php");

// get special modules
require_once("yapf/mail.php");
require_once("yapf/template.php");
require_once("yapf/renderer.php");
require_once("yapf/db/log.php");
require_once("yapf/db/db.php");
require_once("yapf/analytics.php");
require_once("yapf/session.php");
require_once("yapf/acp.php");

?>
