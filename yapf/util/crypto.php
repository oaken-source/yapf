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

/* a simplified interface to a sha512 hash implementation.
 *
 * params:
 *   str - the string to hash
 *
 * returns:
 *   a sha512 hash of the given string, or of a random string if the given
 *   string was NULL. the random string is generated via a call to
 *     openssl_random_pseudo_bytes(64)
 */
function sha512($str = NULL)
{
  if ($str === NULL)
    $str = bin2hex(openssl_random_pseudo_bytes(64));
  return hash("sha512", $str);
}

?>
