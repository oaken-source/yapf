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

class RENDERER
{

  private static $base = "templates/base.php";
  private static $base_args = array();

  private static $content = "";

  private static $title = "";

  private static $extra_js = array();
  private static $extra_css = array();

  private static $page = "";

  private static $http_status = 500;

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
    self::$http_status = 200;
    if (file_exists("pages/" . $page . "/index.php"))
      {
        self::$page = $page;
        yapf_require("pages/" . $page . "/index.php");
      }
    else
      {
        self::$http_status = 404;
        header("HTTP/1.1 404 Not Found");

        if (file_exists("pages/404/index.php"))
          {
            self::$page = "404";
            yapf_require("pages/404/index.php");
          }
        else
          {
            echo "<h1>404 Not Found</h1><p>the page you requested could not be found</p>";
            yapf_exit();
          }
      }

    if (file_exists(self::$base))
      {
        $args = self::$base_args + array(
          'title' => self::$title,
          'content' => self::$content,
          'extra_js' => self::$extra_js,
          'page' => self::$page,
        );

        $template = new T(self::$base, $args);

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

    $_SESSION['yapf']['last_rendered_request_url'] = $_SERVER['REQUEST_URI'];
    yapf_exit();
  }

  public static function getLastRenderedUrl()
  {
    if (isset($_SESSION['yapf']['last_rendered_request_url']))
      return $_SESSION['yapf']['last_rendered_request_url'];

    return "";
  }

  public static function setBaseTemplate($t, $args = array())
  {
    self::$base = "templates/" . $t;
    self::$base_args = $args;
  }

  public static function setTemplate($t, $args = array())
  {
    self::$content = new T("pages/" . self::$page . "/templates/" . $t, $args);
  }

  public static function getStatus()
  {
    return self::$http_status;
  }

  public static function setStatus($http_status)
  {
    self::$http_status = $http_status;
  }

}

?>
