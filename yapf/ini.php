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

class INI
{

  private static $defaults = array();
  private static $config = array();

  public static function init()
  {
    if (file_exists("yapf.ini"));
      self::read();
  }

  private static function read()
  {
    self::$config = parse_ini_file("yapf.ini", true);
  }

  private static function write()
  {
    $output  = "; yapf configuration file\n";
    $output .= ";   this file is managed by yapf, and may be rewritten any time.\n";
    $output .= ";   do not manually add keys or comments here, instead add them to the code!\n\n";

    $tmp = array();
    foreach (self::$config as $skey => $section)
      foreach ($section as $ekey => $element)
        $tmp[$skey][$ekey] = array($ekey, $element, "");

    foreach (self::$defaults as $skey => $section)
      foreach ($section as $default)
        {
          if (!isset($tmp[$skey][$default[0]]))
            $tmp[$skey][$default[0]] = $default;
          $tmp[$skey][$default[0]][2] = $default[2];
        }

    foreach ($tmp as $skey => $section)
      {
        $output .= "[" . $skey . "]\n";
        foreach ($section as $element)
          $output .= "; " . $element[2] . "\n" . $element[0] . " = " . $element[1] . "\n";
        $output .= "\n";
      }

    file_put_contents("yapf.ini", $output);
  }

  public static function get($skey, $ekey, $default = NULL, $comment = "")
  {
    if ($default !== NULL)
      self::$defaults[$skey][] = array($ekey, $default, $comment);

    $section = array();
    if (isset(self::$config[$skey]))
      $section = self::$config[$skey];

    if (isset($section[$ekey]))
      return $section[$ekey];
    
    if ($default !== NULL)
      self::write();

    return $default;
  }

}

INI::init();

// general configuration
date_default_timezone_set(INI::get('yapf', 'default_timezone', 'UTC', 'the default timezone to use'));
ini_set('default_charset', INI::get('yapf', 'default_charset', 'utf-8', 'the default charset to use'));

?>
