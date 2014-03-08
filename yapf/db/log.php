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

  public static function init()
  {
    self::connect();

    // evolve logdb, if necessary
    require_once("yapf/db/evolve_log.php");
  }

  private static function connect()
  {
    self::$handle = mysqli_connect(LOG_SERVER, LOG_DBUSER, LOG_DBPASS);
    assert_fatal(self::$handle, "LOG: unable to connect to database");
    mysqli_select_db(self::$handle, LOG_DBNAME);
  }

  public static function event($loglevel, $message)
  {
    if (LOG_ENABLED === true)
      mysqli_query(self::$handle, "
        insert into __yapf_log_events (loglevel, message) values
          ('" . mysqli_real_escape_string(self::$handle, $loglevel) . "',
           '" . mysqli_real_escape_string(self::$handle, $message) . "')"); 
  }

  public static function getEvents()
  {
    $result = array();

    if (LOG_ENABLED === true)
      {
        $res = mysqli_query(self::$handle, "select * from __yapf_log_events");
        while ($row = mysqli_fetch_assoc($res))
          $result[] = $row;
      }

    return $result;
  }

  public static function clearEvents()
  {
    if (LOG_ENABLED === true)
      mysqli_query(self::$handle, "truncate table __yapf_log_events");
  }

  public static function query($query, $message, $elapsed = 0.0)
  {
    if (LOG_ENABLED === true)
      mysqli_query(self::$handle, "
        insert into __yapf_log_queries (query, message, elapsed) values
          ('" . mysqli_real_escape_string(self::$handle, $query) . "', 
           '" . mysqli_real_escape_string(self::$handle, $message) . "',
           '" . mysqli_real_escape_string(self::$handle, $elapsed) . "')");
  }

  public static function getQueries()
  {
    $result = array();

    if (LOG_ENABLED === true)
      {
        $res = mysqli_query(self::$handle, "select * from __yapf_log_queries");
        while ($row = mysqli_fetch_assoc($res))
          $result[] = $row;
      }

    return $result;
  }

  public static function clearQueries()
  {
    if (LOG_ENABLED === true)
      mysqli_query(self::$handle, "truncate table __yapf_log_queries");
  }

  public static function analytics($totaltime, $http_status)
  {
    if (LOG_ENABLED === true)
      mysqli_query(self::$handle, "
        insert into __yapf_log_analytics (request, referer, remote, totaltime, http_status) values
          ('" . mysqli_real_escape_string(self::$handle, $_SERVER['REQUEST_URI']) . "',
           '" . mysqli_real_escape_string(self::$handle, $_SERVER['HTTP_REFERER']) . "',
           '" . mysqli_real_escape_string(self::$handle, $_SERVER['REMOTE_ADDR']) . "',
           '" . mysqli_real_escape_string(self::$handle, $totaltime) . "',
           '" . mysqli_real_escape_string(self::$handle, $http_status) . "')");
  }

  public static function getAnalytics()
  {
    $result = array();

    if (LOG_ENABLED === true)
      {
        $res = mysqli_query(self::$handle, "select * from __yapf_log_analytics order by id desc");
        while ($row = mysqli_fetch_assoc($res))
          $result[] = $row;
      }

    return $result;
  }

  public static function clearAnalytics()
  {
    if (LOG_ENABLED === true)
      mysqli_query(self::$handle, "truncate table __yapf_log_analytics");
  }

}

?>
