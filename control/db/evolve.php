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

require_once("control/db/dbadmin.php");

// set latest evolution id
$latest_evolution = 1;

// get current db evolution id
$current_evolution = DBADMIN::getEvolutionId();

switch ($current_evolution)
  {
  case -3: // create databases
    DBADMIN::createDatabase();
    redirect_and_exit($_SERVER['REQUEST_URI']);
  case -2: // create settings table    
    DBADMIN::query("
      create table " . LOG::$name . ".settings (`key` VARCHAR(64) PRIMARY KEY, value VARCHAR(64))");
  case -1: // insert db_evolution value
    DBADMIN::query("
      insert into " . LOG::$name . ".settings values ('db_evolution', '0')");
  case 0:
    DBADMIN::query("
      create table " . LOG::$name . ".failed_assertions (
        id INT NOT NULL AUTO_INCREMENT, 
        timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        message VARCHAR(1024) NOT NULL,
        PRIMARY KEY(id))
        ENGINE=INNODB");
    DBADMIN::query("
      create table " . LOG::$name . ".failed_queries (
        id INT NOT NULL AUTO_INCREMENT,
        timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        query VARCHAR(1024) NOT NULL,
        message VARCHAR(1024) NOT NULL,
        PRIMARY KEY(id))
        ENGINE=INNODB");
    DBADMIN::query("
      create table " . LOG::$name . ".analytics (
        id INT NOT NULL AUTO_INCREMENT,
        timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        request VARCHAR(1024) NOT NULL,
        totaltime DOUBLE NOT NULL,
        querycount INT NOT NULL,
        querytime_total DOUBLE NOT NULL,
        querytime_max DOUBLE NOT NULL,
        query_longest VARCHAR(2048) NOT NULL,
        PRIMARY KEY(id))
        ENGINE=INNODB");
    DBADMIN::query("
      create table " . DB::$name . ".news (
        id INT NOT NULL AUTO_INCREMENT,
        timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        message VARCHAR(2048) NOT NULL,
        PRIMARY KEY(id))
        ENGINE=INNODB");
    DBADMIN::query("
      create table " . DB::$name . ".accounts (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(64) NOT NULL,
        email VARCHAR(512) NOT NULL,
        registered TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        lastlogin TIMESTAMP NOT NULL,
        premium TIMESTAMP NOT NULL DEFAULT 0,
        password VARCHAR(128) NOT NULL,
        password_salt VARCHAR(128) NOT NULL,
        PRIMARY KEY(id))
        ENGINE=INNODB");
    DBADMIN::query("
      alter table " . DB::$name . ".accounts
        add column premium TIMESTAMP NOT NULL DEFAULT 0
        after lastlogin");
  case 1: // remote starts here

  }

if (isset($_GET['refetch']))
  {
    DBADMIN::query("
      truncate table " . DB::$name . ".news");
    DBADMIN::query("
      insert into " . DB::$name . ".news
        (timestamp, message)
      select STR_TO_DATE(date, '%d.%m.%Y'), message
        from " . DB::$name . ".RR_News");
  }

// set new db evolution id
DBADMIN::setEvolutionId($latest_evolution);

redirect_and_exit("/index-new.php");

?>
