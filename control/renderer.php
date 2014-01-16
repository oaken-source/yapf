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

class RENDERER
{

  private static $content = "<h1>This is YAPF</h1>";
  private static $notices = array();

  private static $title = "";

  private static $extra_js = array();
  private static $extra_css = array();

  private static $page = "";

  public static function addJS($file)
  {
    self::$extra_js[] = $file;
  }
  
  public static function addCSS($file)
  {
    self::$extra_css[] = $file;
  }

  public static function setTitle($str)
  {
    self::$title = $str;
  }

  public static function renderPage($page)
  {
    if (file_exists("pages/" . $page . "/index.php"))
      {
        self::$page = $page;
        require_once("pages/" . $page . "/index.php");
      }
    elseif (file_exists("pages/404/index.php"))
      {
        self::$page = "404";
        require_once("pages/404/index.php");
      }
    else
      {
        self::$content = "<h1>404 Not Found</h1><p>the page you requested could not be found</p>";
      }

    if (file_exists("templates/base.php"))
      {
        $template = new T("templates/base.php", array(
          'title' => self::$title,
          'notices' => self::$notices,
          'content' => self::$content,
          'extra_js' => self::$extra_js
        ));
        $template->render();
      }
    elseif (self::$content instanceof T)
      {
        self::$content->render();
      }
    else
      {
        echo self::$content;
      }

    ANALYTICS::finish();
  }

  public static function setTemplate($t, $args = array())
  {
    self::$content = new T("pages/" . self::$page . "/templates/" . $t, $args);
  } 

  public static function setError($str)
  {
    self::$notices[] = $str;
  }

}

?>
