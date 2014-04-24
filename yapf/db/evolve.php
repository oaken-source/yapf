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

require_once("yapf/db/dbadmin.php");

class EVOLVE
{

  private static $evolutions = array();
  private static $database = "";

  public static function start($server, $dbuser, $dbpass, $dbname, $schema)
  {
    DBADMIN::connect($server, $dbuser, $dbpass);

    DBADMIN::query("create database if not exists `" . $dbname . "`");
    DBADMIN::query("use `" . $dbname . "`");

    self::$database = $dbname;

    DBADMIN::query("
      create table if not exists `__yapf_evolutions` (
        `identifier` varchar(128) not null,
        `name` varchar(512) not null,
        `evolution` varchar(128) not null,
        primary key(`identifier`))
      engine=innodb");

    $res = DBADMIN::query("select * from `__yapf_evolutions`");
    self::$evolutions = array();
    while ($row = DBADMIN::fetch($res))
      self::$evolutions[$row['identifier']] = $row;

    $schema_id = sha512(($schema['id'] ? $schema['id'] : $schema['name']));
    $schema_hash = sha512(serialize($schema));

    $evolved = False;

    if (self::$evolutions[$schema_id]['evolution'] !== $schema_hash)
      {
        $evolved = True;
        self::evolve_schema($schema, $schema_id, $schema_hash);
      }

    DBADMIN::disconnect();

    return $evolved;
  }

  private static function evolve_schema($schema, $schema_id, $schema_hash)
  {
    foreach ($schema['tables'] as $table)
      {
        $table_id = sha512($schema_id . "." . ($table['id'] ? $table['id'] : $table['name']));
        $table_hash = sha512(serialize($table));

        if (self::$evolutions[$table_id]['evolution'] !== $table_hash)
          self::evolve_table($table, $table_id, $table_hash);
      }

    DBADMIN::query("
      replace into `__yapf_evolutions`
          (`identifier`, `name`, `evolution`)
        values
          (:identifier, :name, :evolution)",

      array(
        ':identifier' => $schema_id,
        ':name' => $schema['name'],
        ':evolution' => $schema_hash,
    ));
  }

  private static function evolve_table($table, $table_id, $table_hash)
  {
    $table_current_name = (self::$evolutions[$table_id]['name'] 
      ? self::$evolutions[$table_id]['name']
      : $table['name']);

    if ($table_current_name !== $table['name'])
      DBADMIN::query("
        rename table `" . $table_current_name . "`
          to `" . $table['name'] . "`");

    self::create_table_if_not_exists($table);
    self::upgrade_table($table);
    self::insert_defaults($table);

    DBADMIN::query("
      replace into `__yapf_evolutions`
          (`identifier`, `name`, `evolution`)
        values
          (:identifier, :name, :evolution)",

      array(
        ':identifier' => $table_id,
        ':name' => $table['name'],
        ':evolution' => $table_hash,
    ));
  }

  private static function create_table_if_not_exists($table)
  {
    $query = "create table if not exists `" . $table['name'] . "` (";

    foreach ($table['columns'] as $column)
      {
        $column['type'] or $column['type'] = 'int';
        $query .= "`" . $column['name'] . "` " . $column['type'] . " not null";
        if ($column['auto_increment'])
          $query .= " auto_increment";
        if ($column['default'])
          $query .= " default " . $column['default'];
        $query .= ", ";
      }

    if ($table['primary_key'])
      $query .= "primary key (`" . $table['primary_key'] . "`)";
    
    $query .= ") engine=" . ($table['engine'] ? $table['engine'] : "innodb");

    DBADMIN::query($query);
  }

  private static function upgrade_table($table)
  {
    DBADMIN::query("
      alter table `" . $table['name'] . "` 
        engine = " . ($table['engine'] ? $table['engine'] : "innodb"));

    $res = DBADMIN::query("
      select * from information_schema.columns 
        where table_schema = '" . self::$database . "'
          and table_name = '" . $table['name'] . "'");
    $db_columns = array();
    while ($row = DBADMIN::fetch($res))
      $db_columns[$row['COLUMN_NAME']] = $row;

    $previous_column = "first";
    foreach ($table['columns'] as $column)
      {
        $column['type'] or $column['type'] = "int";
        if (!$db_columns[$column['name']])
          {
            DBADMIN::query("
              alter table `" . $table['name'] . "` 
                add column `" . $column['name'] . "` 
                  " . $column['type'] . " not null
                  " . ($column['auto_increment'] ? 'auto_increment' : '') . "
                  " . ($column['default'] ? "default '" . $column['default'] . "'" : '') . "
                  " . $previous_column);
          }
        else
          {
            DBADMIN::query("
              alter table `" . $table['name'] . "` 
                change column `" . $column['name'] . "` `" . $column['name'] . "` 
                  " . $column['type'] . " not null
                  " . ($column['auto_increment'] ? 'auto_increment' : '') . "
                  " . ($column['default'] ? "default " . $column['default'] : '') . "
                  " . $previous_column);
          }

        $previous_column = "after `" . $column['name'] . "`";
      } 
  }

  private static function insert_defaults($table)
  {
    $res = DBADMIN::fetch(DBADMIN::query("select count(*) as num_rows from `" . $table['name'] . "`"));
    if ($res['num_rows'] == 0 && isset($table['defaults']))
      {
        foreach ($table['defaults'] as $default)
          {
            DBADMIN::query("
              insert into `" . $table['name'] . "`
                  (`".implode("`, `", array_keys($default))."`)
                values
                  ('".implode("', '", array_values($default))."')");
          }
      }
  }

}

?>
