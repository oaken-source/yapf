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

class RENDERER
{

  private static $content = false;
  private static $notices = array();

  private static $title = "";

  private static $extra_js = array();

  private static $page = "";

  public static function addJavascript($file)
  {
    self::$extra_js[] = $file;
  }

  public static function setTitle($str)
  {
    self::$title = $str;
  }

  public static function renderPage($page)
  {
    if (!file_exists("pages/" . $page . "/index.php"))
      $page = "404";

    self::$page = $page;

    require_once("pages/" . $page . "/index.php");

    $page = new T("templates/base.php", array(
      'title' => self::$title,
      'notices' => self::$notices,
      'content' => self::$content,
      'extra_js' => self::$extra_js
    ));
    $page->render();

    ANALYTICS::finish();
  }

  public static function setTemplate($t)
  {
    self::$content = $t;
  } 

  public static function setError($str)
  {
    self::$notices[] = $str;
  }

}

?>
