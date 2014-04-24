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

class SESSION
{
  
  private static $messages = array();

  public static function init()
  {
    session_start();
    if (isset($_SESSION['messages']))
      self::$messages = unserialize($_SESSION['messages']);
  }

  public static function fini()
  {
    session_unset();
    session_destroy();
  }

  public static function addMessage($message)
  {
    self::$messages[] = $message;
    $_SESSION['messages'] = serialize(self::$messages);
  }

  public static function hasMessages()
  {
    return (!empty(self::$messages));
  }

  public static function getMessages()
  {
    $result = self::$messages;
    self::$messages = array();
    $_SESSION['messages'] = serialize(self::$messages);
    return $result;
  }

}

?>
