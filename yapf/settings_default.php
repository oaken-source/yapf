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

// load user defined settings
defined('SETTINGS_FILE') or define('SETTINGS_FILE', 'settings.php');
if (file_exists(SETTINGS_FILE))
  require_once(SETTINGS_FILE);

// root page for redirection
defined('INDEX_LOCATION') or define('INDEX_LOCATION', '/');

// timezone
defined('DEFAULT_TIMEZONE') or define('DEFAULT_TIMEZONE', 'UTC');
date_default_timezone_set(DEFAULT_TIMEZONE);

// database access controls
defined('LOG_ENABLED') or define('LOG_ENABLED', false);
if (LOG_ENABLED === true)
  {
    assert_fatal(defined('LOG_SERVER') && defined('LOG_DBUSER') && defined('LOG_DBPASS') && defined('LOG_DBNAME'), 
      "invalid settings: LOG_ENABLED set, but one of LOG_SERVER, LOG_DBUSER, LOG_DBPASS unset");
  }
defined('DB_ENABLED') or define('DB_ENABLED', false);
if (DB_ENABLED === true)
  {
    assert_fatal(defined('DB_SERVER') && defined('DB_DBUSER') && defined('DB_DBPASS') && defined('LOG_LOGNAME'), 
      "invalid settings: DB_ENABLED set, but one of DB_SERVER, DB_DBUSER, DB_DBPASS unset");
  }
?>
