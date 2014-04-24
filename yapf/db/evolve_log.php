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

require_once("yapf/db/evolve.php");

// this is the yapf log tables database schema.
$log_schema = array(
  'name' => '__yapf_log_schema',
  'tables' => array(
    array(
      'name' => '__yapf_evolutions',
      'columns' => array(
        array('name' => 'identifier', 'type' => 'varchar(128)'),
        array('name' => 'name', 'type' => 'text'),
        array('name' => 'evolution', 'type' => 'varchar(128)'),
      ),
      'primary_key' => 'identifier'
    ),
    array(
      'name' => '__yapf_log_events',
      'columns' => array(
        array('name' => 'id', 'auto_increment' => true),
        array('name' => 'timestamp', 'type' => 'timestamp', 'default' => 'current_timestamp'),
        array('name' => 'loglevel', 'type' => 'text'),
        array('name' => 'message', 'type' => 'text'),
      ),
      'primary_key' => 'id'
    ),
    array(
      'name' => '__yapf_log_queries_failed',
      'columns' => array(
        array('name' => 'id', 'auto_increment' => true),
        array('name' => 'timestamp', 'type' => 'timestamp', 'default' => 'current_timestamp'),
        array('name' => 'format', 'type' => 'text'),
        array('name' => 'arguments', 'type' => 'text'),
        array('name' => 'message', 'type' => 'text'),
      ),
      'primary_key' => 'id'
    ),
    array(
      'name' => '__yapf_log_queries_profile',
      'columns' => array(
        array('name' => 'id', 'auto_increment' => true),
        array('name' => 'timestamp', 'type' => 'timestamp', 'default' => 'current_timestamp'),
        array('name' => 'format', 'type' => 'text'),
        array('name' => 'arguments', 'type' => 'text'),
        array('name' => 'prepare_time', 'type' => 'double'),
        array('name' => 'execute_time', 'type' => 'double'),
      ),
      'primary_key' => 'id'
    ),
    array(
      'name' => '__yapf_log_analytics',
      'columns' => array(
        array('name' => 'id', 'auto_increment' => true),
        array('name' => 'timestamp', 'type' => 'timestamp', 'default' => 'current_timestamp'),
        array('name' => 'request', 'type' => 'text'),
        array('name' => 'referer', 'type' => 'text'),
        array('name' => 'remote', 'type' => 'varchar(16)'),
        array('name' => 'totaltime', 'type' => 'double'),
        array('name' => 'http_status', 'type' => 'int')
      ),
      'primary_key' => 'id'
    ),
  ),
);

EVOLVE::start(LOG_SERVER, LOG_DBUSER, LOG_DBPASS, LOG_DBNAME, $log_schema);

?>
