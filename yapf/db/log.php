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

class LOG
{

  private static $handle;

  public static function connect()
  {
    self::$handle = new PDO("mysql:host=" . LOG_SERVER . ";dbname=" . LOG_DBNAME, LOG_DBUSER, LOG_DBPASS);
    assert_fatal(self::$handle, "LOGDB: unable to connect to database");

    // evolve logdb, if necessary
    require_once("yapf/db/evolve_log.php");
  }

  public static function event($loglevel, $message)
  {
    if (LOG_ENABLED !== true)
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_events 
            (loglevel, message) 
          values
            (:loglevel, :message)");

    $statement->execute(array(
      ':loglevel' => $loglevel,
      ':message' => $message,
    ));
  }

  public static function query_failed($format, $arguments, $message)
  {
    if (LOG_ENABLED !== true)
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_queries_failed 
            (format, arguments, message) 
          values 
            (:format, :arguments, :message)");

    $statement->execute(array(
      ':format' => $format,
      ':arguments' => serialize($arguments),
      ':message' => $message,
    ));
  }

  public static function query_profile($format, $arguments, $prepare_time, $execute_time)
  {
    if (LOG_ENABLED !== true)
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_queries_profile
            (format, arguments, prepare_time, execute_time) 
          values 
            (:format, :arguments, :prepare_time, :execute_time)");

    $statement->execute(array(
      ':format' => $format,
      ':arguments' => serialize($arguments),
      ':prepare_time' => $prepare_time,
      ':execute_time' => $execute_time,
    ));
  }

  public static function analytics($totaltime, $http_status)
  {
    if (LOG_ENABLED !== true)
      return;
    
    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_analytics 
            (request, referer, remote, totaltime, http_status) 
          values
            (:request, :referer, :remote, :totaltime, :http_status)");

    $statement->execute(array(
      ':request' => $_SERVER['REQUEST_URI'],
      ':referer' => (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ""),
      ':remote' => $_SERVER['REMOTE_ADDR'],
      ':totaltime' => $totaltime,
      ':http_status' => $http_status,
    ));
  }

}



?>
