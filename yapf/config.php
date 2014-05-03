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

class CONFIG
{

  private static $config = array();

  public static function init()
  {
    if (file_exists("yapf/config.cnf"))
      self::$config = unserialize(file_get_contents("yapf/config.cnf"));
  }

  public static function write()
  {
    file_put_contents("yapf/config.cnf", serialize(self::$config));
  }

  public static function get($key, $default = NULL)
  {
    if (!isset(self::$config[$key]))
      {
        self::$config[$key] = $default;
        self::write();
      }

    return self::$config[$key];
  }

  public static function getAll()
  {
    return self::$config;
  }

  public static function set($key, $value)
  {
    self::$config[$key] = $value;
    self::write();
  }

  public static function delete($key)
  {
    unset(self::$config[$key]);
    self::write();
  }

}

CONFIG::init();

?>
