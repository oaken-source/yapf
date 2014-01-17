<?php require_once($_SERVER['DOCUMENT_ROOT']."/yapf/valid_request.php");

/******************************************************************************
 *                                    yapf                                    *
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

// load settings
require_once("yapf/settings_default.php");

// load helper methods and classes
require_once("yapf/util.php");
require_once("yapf/session.php");
require_once("yapf/mail.php");
require_once("yapf/template.php");
require_once("yapf/renderer.php");
require_once("yapf/analytics.php");
require_once("yapf/db/log.php");
require_once("yapf/db/db.php");

// initialize
if (LOG_ENABLED === true)
  LOG::init();
if (DB_ENBLED === true)
  DB::init();

SESSION::init();

?>
