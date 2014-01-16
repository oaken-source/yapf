<?php require_once($_SERVER['DOCUMENT_ROOT']."/control/valid_request.php");

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

class DBADMIN
{

  private static $handle;

  public static function connect()
  {
    require("control/db/access.php");
    self::$handle = mysqli_connect($dbadmin_server, $dbadmin_user, $dbadmin_pass);
    assert_fatal(self::$handle, "DBADMIN: unable to connect to database");
  }

  // get the current evolution id.
  // special values:
  //   -3 : database does not exist [commented out]
  //   -2 : settings table does not exist
  //   -1 : evolution value not in settings database
  //   any nonnegative integer: regular evolution id
  public static function getEvolutionId()
  {
    $res = mysqli_query(self::$handle, " 
      select SCHEMA_NAME from INFORMATION_SCHEMA.SCHEMATA 
        where SCHEMA_NAME = '" . LOG::$name . "'");
    if ($res && !mysqli_num_rows($res))
      return -3;
 
    //if (!mysqli_num_rows(self::query("
    //  select TABLE_NAME from INFORMATION_SCHEMA.TABLES 
    //    where TABLE_SCHEMA = '" . LOG::$name . "' 
    //    and TABLE_NAME = 'settings'")))
    //  return -2;

    $res = mysqli_query(self::$handle, "
      select value from " . LOG::$name . ".settings 
        where `key` = 'db_evolution'");
    if (!$res)
      return -2;
    elseif (!mysqli_num_rows($res))
      return -1;
  
    $evolution = mysqli_fetch_assoc($res);
    return $evolution['value'];
  }

  // set the new evolution id
  public static function setEvolutionId($id)
  {
    self::query("
      update " . LOG::$name . ".settings 
        set value = '".$id."' 
        where `key` = 'db_evolution'");
  }

  // creates initial datases - used in evolution -3
  public static function createDatabase()
  {
    require("control/db/access.php");
    self::query("CREATE DATABASE " . LOG::$name);
    self::query("CREATE DATABASE " . DB::$name);
  }

  // no checking is performed - use with care, or not at all!
  public static function query($str)
  {
    $res = mysqli_query(self::$handle, $str);
    assert_fatal($res, "DBADMIN: query failed: " . mysqli_error(self::$handle) . "<br>\n"
        . "query was: \"" . $str . "\"<br>\n");

    return $res;
  }

}

DBADMIN::connect();

?>
