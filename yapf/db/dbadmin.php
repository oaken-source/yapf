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

class DBADMIN
{

  private static $handle;

  public static function connect($server, $dbuser, $dbpass)
  {
    self::$handle = new PDO("mysql:host=" . $server, $dbuser, $dbpass,
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    assert_fatal(self::$handle, "DBADMIN: unable to connect to database");
  }

  public static function disconnect()
  {
    self::$handle = NULL;
  }

  public static function query($format, $arguments = array())
  {
    $statement = self::$handle->prepare($format);
    if (!$statement)
      {
        $error = self::$handle->errorInfo();
        assert_fatal(0, "DBADMIN: [" . $error[0] . "] " . $error[2] . ": " . $format);
      }

    $res = $statement->execute($arguments);
    if (!$res)
      {
        $error = $statement->errorInfo();
        assert_fatal(0, "DBADMIN: [". $error[0] . "] " . $error[2] . ": " . $format);
      }

    return $statement;
  }

  public static function fetch($statement)
  {
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

}

?>
