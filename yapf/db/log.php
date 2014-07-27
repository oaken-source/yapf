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

  private static $handle = NULL;

  public static function connect()
  {
    $server = INI::get('yapf', 'logging_database_server', '', 'the server of the logging database');
    $dbname = INI::get('yapf', 'logging_database_name', '', 'the name of the logging database');
    $dbuser = INI::get('yapf', 'logging_database_username', '', 'the username of the logging database');
    $dbpass = INI::get('yapf', 'logging_database_password', '', 'the password of the logging database');

    if (!$server || !$dbname || !$dbuser || !$dbpass)
      return;

    self::$handle = new PDO("mysql:host=" . $server . ";dbname=" . $dbname, $dbuser, $dbpass);
    assert_fatal(self::$handle, "LOGDB: unable to connect to database");

    // always evolve logdb, if necessary
    require_once("yapf/db/evolve_log.php");
    require_once("yapf/db/evolve.php");
    EVOLVE::start($server, $dbuser, $dbpass, $dbname, $log_schema);
  }

  public static function isConnected()
  {
    return self::$handle !== NULL;
  }

  public static function event($loglevel, $message)
  {
    if (!self::isConnected())
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

  public static function query_failed($format, $source, $arguments, $message)
  {
    if (!self::isConnected())
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_queries_failed
            (format, source, arguments, message)
          values
            (:format, :source, :arguments, :message)");

    $statement->execute(array(
      ':format' => $format,
      ':source' => $source,
      ':arguments' => serialize($arguments),
      ':message' => $message,
    ));
  }

  public static function query_profile($format, $source, $arguments, $prepare_time, $execute_time)
  {
    if (!self::isConnected())
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_queries_profile
            (request_id, format, source, arguments, prepare_time, execute_time)
          values
            (:request_id, :format, :source, :arguments, :prepare_time, :execute_time)");

    $statement->execute(array(
      ':request_id' => ANALYTICS::getRequestId(),
      ':format' => $format,
      ':source' => $source,
      ':arguments' => serialize($arguments),
      ':prepare_time' => $prepare_time,
      ':execute_time' => $execute_time,
    ));
  }

  public static function analytics_start($request_uri, $request_class, $referer, $remote, $post_array)
  {
    if (!self::isConnected())
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        insert into __yapf_log_analytics
            (request_uri, request_class, referer, remote, post_array)
          values
            (:request_uri, :request_class, :referer, :remote, :post_array)");

    $statement->execute(array(
      ':request_uri' => $request_uri,
      ':request_class' => $request_class,
      ':referer' => $referer,
      ':remote' => $remote,
      ':post_array' => $post_array,
    ));

    return self::$handle->lastInsertId();
  }

  public static function analytics_finish($request_id, $totaltime, $http_status)
  {
    if (!self::isConnected())
      return;

    static $statement = NULL;
    if ($statement == NULL)
      $statement = self::$handle->prepare("
        update __yapf_log_analytics
          set totaltime = :totaltime,
            http_status = :http_status
          where id = :request_id");

    $statement->execute(array(
      ':totaltime' => $totaltime,
      ':http_status' => $http_status,
      ':request_id' => $request_id,
    ));

    return self::$handle->lastInsertId();
  }

}

LOG::connect();

?>
