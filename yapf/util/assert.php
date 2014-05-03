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

/* check a given condition and die horribly if not met.
 *
 * params:
 *   condition - the condition to test
 *   message - the message to print
 */
function assert_fatal($condition, $message)
{
  if (!$condition)
    {
      RENDERER::setStatus(500);
      LOG::event("FATAL", $message);
      header("HTTP/1.1 500 Internal Server Error");
      echo '<h1>500 - Internal Server Error</h1>';
      echo '<p><b>FATAL:</b> ' . htmlentities($message) . '</p>';
      yapf_exit();
    }
}

?>
