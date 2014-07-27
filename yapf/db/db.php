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

class DB
{

  private static $handle = NULL;

  public static function connect()
  {
    $server = INI::get('yapf', 'general_database_server', '', 'the server of the general database');
    $dbname = INI::get('yapf', 'general_database_name', '', 'the name of the general database');
    $dbuser = INI::get('yapf', 'general_database_username', '', 'the username of the general database');
    $dbpass = INI::get('yapf', 'general_database_password', '', 'the password of the general database');

    if (!$server || !$dbname || !$dbuser || !$dbpass)
      return;

    self::$handle = new PDO("mysql:host=" . $server . ";dbname=" . $dbname, $dbuser, $dbpass,
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

    assert_fatal(self::$handle, "DB: unable to connect to database");
  }

  public static function isConnected()
  {
    return self::$handle !== NULL;
  }

  public static function setSchema($schema)
  {
    // evolve db, if necessary
    require_once("yapf/db/evolve.php");

    $server = INI::get('yapf', 'general_database_server', '', 'the server of the general database');
    $dbname = INI::get('yapf', 'general_database_name', '', 'the name of the general database');
    $dbuser = INI::get('yapf', 'general_database_username', '', 'the username of the general database');
    $dbpass = INI::get('yapf', 'general_database_password', '', 'the password of the general database');

    if (!$server || !$dbname || !$dbuser || !$dbpass)
      return;

    return EVOLVE::start($server, $dbuser, $dbpass, $dbname, $schema);
  }

  public static function query($format, $arguments = array())
  {
    assert_fatal(self::isConnected(), "Database currently disabled - invalid or missing configuration?");

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $source = $backtrace[0]['file'] . ':' . $backtrace[0]['line'];

    if (!is_array($arguments))
      {
        LOG::query_failed($format, $source, $arguments, "[-1] \$arguments is expected to be an array");
        return false;
      }

    $prepare_time = microtime(true);

    $statement = self::$handle->prepare($format);
    if (!$statement)
      {
        $error = self::$handle->errorInfo();
        LOG::query_failed($format, $source, $arguments, "[" . $error[0] . "] " . $error[2]);
        return false;
      }

    $prepare_time = microtime(true) - $prepare_time;

    $execute_time = microtime(true);

    $res = $statement->execute($arguments);
    if (!$res)
      {
        $error = $statement->errorInfo();
        LOG::query_failed($format, $source, $arguments, "[" . $error[0] . "] " . $error[2]);
        return false;
      }

    $execute_time = microtime(true) - $execute_time;

    LOG::query_profile($format, $source, $arguments, $prepare_time, $execute_time, 1);

    return $statement;
  }

  public static function quote($value)
  {
    if (is_array($value))
      return array_map(array(self::$handle, 'quote'), $value);
    return self::$handle->quote($value);
  }

  public static function fetch($statement)
  {
    if ($statement instanceof PDOStatement)
      return $statement->fetch(PDO::FETCH_ASSOC);
    return false;
  }

  public static function fetchAll($statement)
  {
    if ($statement instanceof PDOStatement)
      return $statement->fetchAll(PDO::FETCH_ASSOC);
    return false;
  }

  public static function insertId()
  {
    return self::$handle->lastInsertId();
  }

}

DB::connect();

?>
