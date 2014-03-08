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

class DBADMIN
{

  private static $handle;

  public static function connect($server, $dbuser, $dbpass)
  {
    self::$handle = mysqli_connect($server, $dbuser, $dbpass);
    assert_fatal(self::$handle, "DBADMIN: unable to connect to database");
  }

  public static function disconnect()
  {
    mysqli_close(self::$handle);
  }

  public static function escape($x)
  {
    return mysqli_real_escape_string(self::$handle, $x);
  }

  // no checking is performed - use with care, or not at all!
  public static function query($str)
  {
    $res = mysqli_query(self::$handle, $str);
    assert_fatal($res, "DBADMIN: query failed: " . mysqli_error(self::$handle) . "<br>\n"
        . "query was: \"" . $str . "\"<br>\n");

    return $res;
  }

  public static function fetch($res)
  {
    return mysqli_fetch_assoc($res);
  }

  public static function rows($res)
  {
    return mysqli_num_rows($res);
  }

}

?>
