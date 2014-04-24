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

class DB
{
  
  private static $handle;

  public static function init()
  {
    self::$handle = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DBNAME, DB_DBUSER, DB_DBPASS,
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    assert_fatal(self::$handle, "DB: unable to connect to database");
  }

  public static function setSchema($schema)
  {
    // evolve db, if necessary
    require_once("yapf/db/evolve.php");

    return EVOLVE::start(DB_SERVER, DB_DBUSER, DB_DBPASS, DB_DBNAME, $schema);
  }

  public static function query($format, $arguments = array())
  {
    assert_fatal(DB_ENABLED === true, "DB_ENABLED not set, but DB::query(...) used. fix your settings");

    if (!is_array($arguments))
      {
        LOG::query_failed($format, $arguments, "[-1] \$arguments is expected to be an array");
        return false;
      }
  
    $prepare_time = microtime(true);

    $statement = self::$handle->prepare($format);
    if (!$statement)
      {
        $error = self::$handle->errorInfo();
        LOG::query_failed($format, $arguments, "[" . $error[0] . "] " . $error[2]);
        return false;
      }
      
    $prepare_time = microtime(true) - $prepare_time;

    $execute_time = microtime(true);

    $res = $statement->execute($arguments);
    if (!$res)
      {
        $error = $statement->errorInfo();
        LOG::query_failed($format, $arguments, "[" . $error[0] . "] " . $error[2]);
        return false;
      }

    $execute_time = microtime(true) - $execute_time;

    LOG::query_profile($format, $arguments, $prepare_time, $execute_time, 1);

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

?>
