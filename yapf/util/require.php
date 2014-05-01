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

/* assert that a file passes the internal yapf security requirements and raise 
 * a fatal error on failure
 * 
 * params:
 *   filename - the name of the file to check
 */
function check_file_integrity($filename)
{
  $file = fopen($filename, 'r');
  $line = fgets($file);
  fclose($file);

  assert_fatal(
    strpos($line, '<?php require_once($_SERVER[\'DOCUMENT_ROOT\']."/yapf/valid_request.php");') === 0,
    'included file `' . $filename . '` not under request control. (add `<?php yapf_require_once($_SERVER[\'DOCUMENT_ROOT\']."/yapf/valid_request.php");`)');
}

/* assert that a file passes the internal yapf security requirements and raise 
 * a fatal error on failure.
 * on success, include the file via require_once
 * 
 * params:
 *   filename - the name of the file to check and require
 */
function yapf_require_once($filename)
{
  check_file_integrity($filename);
  require_once($filename);
}

/* assert that a file passes the internal yapf security requirements and raise 
 * a fatal error on failure.
 * on success, include the file via require
 * 
 * params:
 *   filename - the name of the file to check and require
 */
function yapf_require($filename)
{
  check_file_integrity($filename);
  require($filename);
}

/* assert that a file passes the internal yapf security requirements and raise 
 * a fatal error on failure.
 * on success, include the file via include_once
 * 
 * params:
 *   filename - the name of the file to check and include
 */
function yapf_include_once($filename)
{
  check_file_integrity($filename);
  include_once($filename);
}

/* assert that a file passes the internal yapf security requirements and raise 
 * a fatal error on failure.
 * on success, include the file via include
 * 
 * params:
 *   filename - the name of the file to check and include
 */
function yapf_include($filename)
{
  check_file_integrity($filename);
  include($filename);
}

?>
