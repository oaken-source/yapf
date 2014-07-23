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

/* exit the script, and do some cleanup beforehand
 */
function yapf_exit()
{
  if (class_exists('ANALYTICS'))
    ANALYTICS::finish();
  exit();
}

/* redirect to a given location and exit the script
 *
 * params:
 *   location - the location to redirect to
 */
function redirect_and_exit($location = "/")
{
  header("Location: " . $location);
  RENDERER::setStatus(302);
  yapf_exit();
}

/* redirect to the last successfully rendered page and exit the script
 */
function redirect_back_and_exit()
{
  redirect_and_exit(RENDERER::getLastRenderedUrl());
}

?>
