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

class LOG
{

  private static $handle;

  public static $name;

  public static function connect()
  {
    require("yapf/db/access.php");
    self::$handle = mysqli_connect($logdb_server, $logdb_user, $logdb_pass);
    assert_fatal(self::$handle, "LOG: unable to connect to database");
    mysqli_select_db(self::$handle, $logdb_name);
    self::$name = $logdb_name;
  }

  public static function event($loglevel, $message)
  {
    mysqli_query(self::$handle, "
      insert into log_events (loglevel, message) values
        ('" . mysqli_real_escape_string(self::$handle, $loglevel) . "',
         '" . mysqli_real_escape_string(self::$handle, $message) . "')"); 
  }

  public static function query($query, $message)
  {
    mysqli_query(self::$handle, "
      insert into log_queries (query, message) values
        ('" . mysqli_real_escape_string(self::$handle, $query) . "', 
         '" . mysqli_real_escape_string(self::$handle, $str) . "')");
  }

  public static function analytics($totaltime)
  {
    mysqli_query(self::$handle, "
      insert into analytics (request, totaltime) values
        ('" . mysqli_real_escape_string(self::$handle, $_SERVER['REQUEST_URI']) . "',
         '" . mysqli_real_escape_string(self::$handle, $totaltime) . "')");
  }

}

LOG::connect();

?>
