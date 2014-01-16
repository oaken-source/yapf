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


/* check if a given value is contained in a given array, and return the array
 * version, if found
 *
 * params:
 *   needle - the search value
 *   haystack - the array to search
 * 
 * returns:
 *   a value from the array, if a match is found, NULL otherwise
 */
function sanitize_from_array($needle, $haystack, $default = NULL)
{
  $result = $default;

  $key = array_search($needle, $haystack, TRUE);
  if ($key !== FALSE && $key !== NULL)
    $result = $haystack[$key];

  return $result;
}

/* redirect to a given location and exit the script
 *
 * params:
 *   location - the location to redirect to
 */
function redirect_and_exit($location = "/")
{
  ANALYTICS::finish();
  header("Location: " . $location);
  exit();
}

/* check a given condition and log an event to admin database if not met.
 *
 * params:
 *   condition - the condition to test
 *   message - the message to log
 */
function assert_log($condition, $message)
{
  if (!$condition)
    LOG::failedAssert("[RELOCATE] " . $message);
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
function assert_relocate($condition, $message, $location = "/")
{
  if (!$condition)
    {
      LOG::failedAssert("[RELOCATE] " . $message);
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
      LOG::failedAssert("[FATAL] " . $message);
      ANALYTICS::finish();
      echo '<b>FATAL:</b> ' . $message;
      exit();
    }
}

/* set a timed redirect to a given location
 * 
 * params:
 *   delay - the time to wait before redirecting in seconds
 *   location - the location to redirect to
 */
function redirect_delayed($delay, $location = "/")
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

/* generate a human readable string representing a time period
 *
 * params:
 *   t - the time period in seconds
 *
 * returns:
 *   a string representing the time passed, e.g. '2 months ago', 'now'
 */
function human_readable_time_interval($t)
{
  $t = (int)$t;

  if ($t <= 60)
    return $t . " Sekunde" . ($t > 1 ? 'n' : '');
  
  $t = (int)($t / 60);

  if ($t < 60)
    return $t . " Minute" . ($t > 1 ? 'n' : '');

  $t = (int)($t / 60);

  if ($t < 24)
    return $t . " Stunde" . ($t > 1 ? 'n' : '');

  $t = (int)($t / 24);

  if ($t < 7)
    return $t . " Tag" . ($t > 1 ? 'en' : '');

  if ($t < 30)
    {
      $tmp = (int)($t / 7);
      return $tmp . " Woche" . ($tmp > 1 ? 'n' : '');
    }

  if ($t < 365)
    {
      $tmp = (int)($t / 30);
      return $tmp . " Monat" . ($tmp > 1 ? 'en' : '');
    }

  $t = (int)($t / 365);
  return $t . " Jahr" . ($t > 1 ? 'en' : '');
}

?>
