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

class ANALYTICS
{

  private static $request_id = 0;

  public static function start()
  {
    $request_uri = $_SERVER['REQUEST_URI'];
    $request_class = self::strip($_SERVER['REQUEST_URI']);
    $referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");
    $remote = $_SERVER['REMOTE_ADDR'];
    $post_array = serialize(isset($_POST) ? $_POST : array());

    self::$request_id = LOG::analytics_start($request_uri, $request_class, $referer, $remote, $post_array);
  }

  public static function finish()
  {
    $totaltime = microtime(true) - SCRIPT_START;

    LOG::analytics_finish(self::$request_id, $totaltime, RENDERER::getStatus());
  }

  private static function strip($uri)
  {
    return preg_replace('/([^e]|[^g]e|[^a]ge|[^p]age|[^?&]page)=[^&]*/', '$1', $uri);
  }

  public static function getRequestId()
  {
    return self::$request_id;
  }

}

ANALYTICS::start();

?>
