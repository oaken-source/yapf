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

class APC
{

  private static $dir = NULL;

  public static function init()
  {
    self::$dir = dirname(__FILE__) . "/apc/";
    if (!is_dir(self::$dir))
      mkdir(self::$dir, 0777, true);
  }

  public static function schedule($function, $delay, $arguments = array())
  {
    $identifier = sha512();

    $start_time = SCRIPT_START;
    $scheduled_end_time = $start_time + $delay;

    $apc = array(
      'identifier' => $identifier,
      'function' => $function,
      'start_time' => $start_time,
      'scheduled_end_time' => $scheduled_end_time,
      'arguments' => $arguments,
    );

    $apc_filename = self::$dir . "/" . $identifier . ".apc";
    file_put_contents($apc_filename, serialize($apc));

    $min = floor($scheduled_end_time / 60) - floor($start_time / 60);
    $sec = ($min ? floor($scheduled_end_time % 60) : $delay);

    $command = 'echo "sleep ' . $sec . '; curl http://' . $_SERVER['SERVER_NAME'] . '/?apc=' . $identifier . '" | at now+' . $min . 'minutes 2>&1';
    $res = shell_exec($command);

    $matches;
    if (!preg_match('/job ([0-9]*) at/', $res, $matches))
      {
        LOG::event('APC_FAILURE', 'failed to spawn APC: ' . $res);
        return NULL;
      }

    // unused:
    $job_id = $matches[1];

    return $identifier;
  }

  public static function cancel($identifier)
  {
    return unlink(self::$dir . "/" . $identifier . ".apc");
  }

  public static function process_callback_and_exit($identifier)
  {
    $apc_filename = self::$dir . "/" . $identifier . ".apc";
    if (!file_exists($apc_filename))
      LOG::event('APC_FAILURE', 'apc-file of invalid id requested');

    $apc = unserialize(file_get_contents($apc_filename));
    $_SESSION['apc'] = $apc;

    yapf_require_once("callback/" . $apc['function'] . "/index.php");

    yapf_exit();
  }

}

APC::init();

?>
