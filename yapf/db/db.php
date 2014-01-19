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
    self::connect();
  }

  private static function connect()
  {
    self::$handle = mysqli_connect(DB_SERVER, DB_DBUSER, DB_DBPASS);
    assert_fatal(self::$handle, "DB: unable to connect to database");
    mysqli_select_db(self::$handle, DB_DBNAME);
  }

  public static function setSchema($schema)
  {
    // evolve db, if necessary
    require_once("yapf/db/evolve.php");

    EVOLVE::start(DB_SERVER, DB_DBUSER, DB_DBPASS, DB_DBNAME, $schema);
  }

  public static function escape($str)
  {
    return mysqli_real_escape_string(self::$handle, $str);
  }

  public static function query($format)
  {
    assert_fatal(DB_ENABLED === true, "DB_ENABLED not set, but DB::query(...) used. fix your settings");

    $query = $format . ' ';

    $argc = func_num_args();
    $argv = func_get_args();

    $index_query = 0;
    $index_args = 1;

    while (($index_query = strpos($query, '%', $index_query)) !== false)
      {
        switch ($query[$index_query + 1])
          {
          case '%':
            $query = substr_replace($query, '', $index_query, 1);
            $index_query++;
            break;
          case 'e':
            if ($index_args >= $argc)
              {
                LOG::query($format, "not enough arguments for format");
                return false;
              }
            $query = substr_replace($query, DB::escape($argv[$index_args]), $index_query, 2);
            $index_query += strlen($argv[$index_args]);
            $index_args++;
            break;
          case 'u':
            if ($index_args >= $argc)
              {
                LOG::query($format, "not enough arguments for format");
                return false;
              }
            $query = substr_replace($query, $argv[$index_args], $index_query, 2);
            $index_query += strlen($argv[$index_args]);
            $index_args++;
            break;
          default:
            LOG::query($format, "unknown yapf sequence '%" . $query[$index_query + 1] . "'");
            return false;
          }
      }

    if ($index_args != $argc)
      {
        LOG::query($format, "too many arguments for format");
        return false;
      }

    $res = mysqli_query(self::$handle, $query);
    if (!$res)
      LOG::query($query, mysqli_error(self::$handle));

    return $res;
  }

  public static function fetch($query)
  {
    return mysqli_fetch_assoc($query);
  }

  public static function rows($query)
  {
    return mysqli_num_rows($query);
  }

  public static function insertId()
  {
    return mysqli_insert_id(self::$handle);
  }

}

?>
