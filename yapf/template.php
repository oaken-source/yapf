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

class T
{

  private $file;
  private $args;

  private static function filter(&$val)
  {
    if ($val instanceof T)
      return;

    if (is_object($val))
      {
        LOG::event('TEMPLATE-ERROR', "passed invalid type '" . gettype($val) . "' to template");
        $val = NULL;
      }

    $val = htmlspecialchars($val);
  }

  public function __construct($file = "", $args = array())
  {
    $this->file = $file;
    $this->args = $args;

    array_walk_recursive($this->args, 'self::filter');
  }

  public function __get($key)
  {
    return $this->args[$key];
  }

  public function __set($key, $value)
  {
    $this->args[$key] = $value;
  }

  public function render()
  {
    check_file_integrity($this->file);
    require($this->file);
  }

}

?>
