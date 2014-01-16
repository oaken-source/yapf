<?php require_once($_SERVER['DOCUMENT_ROOT']."/control/valid_request.php");

/******************************************************************************
 *                           knights of kalindor                              *
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
 
  private static $querylog = array();

  public static $name;

  public static function connect()
  {
    require($_SERVER['DOCUMENT_ROOT']."/control/db/access.php");
    self::$handle = mysqli_connect($gamedb_server, $gamedb_user, $gamedb_pass);
    assert_fatal(self::$handle, "DB: unable to connect to database");
    mysqli_select_db(self::$handle, $gamedb_name);
    self::$name = $gamedb_name;
  }

  public static function escape($str)
  {
    return mysqli_real_escape_string(self::$handle, $str);
  }

  public static function query($format)
  {
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
                LOG::failedQuery($format, "not enough arguments for format");
                return false;
              }
            $query = substr_replace($query, DB::escape($argv[$index_args]), $index_query, 2);
            $index_query += strlen($argv[$index_args]);
            $index_args++;
            break;
          case 'u':
            if ($index_args >= $argc)
              {
                LOG::failedQuery($format, "not enough arguments for format");
                return false;
              }
            $query = substr_replace($query, $argv[$index_args], $index_query, 2);
            $index_query += strlen($argv[$index_args]);
            $index_args++;
            break;
          default:
            LOG::failedQuery($format, "unknown control sequence '%" . $query[$index_query + 1] . "'");
            return false;
          }
      }

    if ($index_args != $argc)
      {
        LOG::failedQuery($format, "too many arguments for format");
        return false;
      }

    $start = microtime(true);

    $res = mysqli_query(self::$handle, $query);
    if (!$res)
      LOG::failedQuery($query, mysqli_error(self::$handle));

    $elapsed = microtime(true) - $start;

    self::$querylog[] = array('query' => $query, 'elapsed' => $elapsed);

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

  public static function getQueryLog()
  {
    return self::$querylog;
  }
}

DB::connect();

?>
