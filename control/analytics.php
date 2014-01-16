<?php require_once($_SERVER['DOCUMENT_ROOT']."/control/valid_request.php");

/******************************************************************************
 *                           knights of kalindor                              *
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

class ANALYTICS
{

  private static $time_start = SCRIPT_START;

  public static function finish()
  {
    $querycount = 0;
    $querytime_total = 0;
    $querytime_max = 0;
    $query_longest = "";

    $querylog = DB::getQueryLog();
    foreach($querylog as $query)
      {
        ++$querycount;
        $querytime_total += $query['elapsed'];
        if ($query['elapsed'] > $querytime_max)
          {
            $querytime_max = $query['elapsed'];
            $query_longest = $query['query'];
          }
      }

    // TODO: html validation

    $totaltime = microtime(true) - self::$time_start;

    LOG::analytics($totaltime, $querycount, $querytime_total, $querytime_max, $query_longest);
  }

}

?>
