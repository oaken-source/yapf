<?php require_once($_SERVER['DOCUMENT_ROOT']."/yapf/valid_request.php");

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


/* redirect to a given location and exit the script
 *
 * params:
 *   location - the location to redirect to
 */
function redirect_and_exit($location = INDEX_LOCATION)
{
  if (class_exists('ANALYTICS'))
    ANALYTICS::finish(302);
  header("Location: " . $location);
  exit();
}

/* check a given condition and redirect to the given location (default /)
 * if the condition is not met. Also, will log an event to admin database if
 * a message was specified.
 *
 * params:
 *   condition - the condition to test
 *   message - the message to log
 *   location - the location to redirect to
 */
function assert_relocate($condition, $message, $location = INDEX_LOCATION)
{
  if (!$condition)
    {
      if (class_exists('LOG'))
        LOG::event("RELOCATE", $message);
      redirect_and_exit($location);
    }
}

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
      if (class_exists('LOG'))
        LOG::event("FATAL", $message);
      if (class_exists('ANALYTICS'))
        ANALYTICS::finish(500);
      header("HTTP/1.1 500 Internal Server Error");
      echo '<h1>500 - Internal Server Error</h1>';
      echo '<p><b>FATAL:</b> ' . htmlentities($message) . '</p>';
      exit();
    }
}

/* set a timed redirect to a given location
 * 
 * params:
 *   delay - the time to wait before redirecting in seconds
 *   location - the location to redirect to
 */
function redirect_delayed($delay, $location = INDEX_LOCATION)
{
  header("Refresh: " . $delay . "; URL=" . $location);
}

/* simplified interface to the sha512 method
 * 
 * params:
 *   str - th str to hash, if NULL, a random string is used
 */
function sha512($str = NULL)
{
  if ($str === NULL)
    $str = bin2hex(openssl_random_pseudo_bytes(64));
  return hash("sha512", $str);
}

/* check a given file for a set of features required for yapf interoperability
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

?> 
