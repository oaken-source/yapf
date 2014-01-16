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

class SESSION
{

  public static function init()
  {
    ini_set('session.gc_probability', 0);
    ini_set('session.save_path', 'sessions');

    session_start();
    if (isset($_SESSION['account']['id']))
      {
        $res = DB::query("
          select id, UNIX_TIMESTAMP(premium) as premium
            from accounts 
            where id = '%e'", $_SESSION['account']['id']);
        
        if (!DB::rows($res))
          self::logout();

        $row = DB::fetch($res);
        self::update($row);
      }
  }

  public static function fini()
  {
    session_unset();
    session_destroy();
  }

  public static function isLoggedIn()
  {
    return (isset($_SESSION['account']['id']));
  }

  public static function isPremium()
  {
    return (isset($_SESSION['account']['premium']) && $_SESSION['account']['premium'] > SCRIPT_START);
  }

  public static function login($accountname, $password)
  {
    $res = DB::query("
      select id, password, password_salt, UNIX_TIMESTAMP(premium) as premium
        from accounts 
        where name = '%e'", $accountname);
    if (!DB::rows($res))
      return false;

    $row = DB::fetch($res);

    $pass_valid = sha512($row['password_salt'] . $password);
    if ($pass_valid !== $row['password'])
      return false;

    self::update($row);

    return true;
  }

  private static function update($account)
  {
    $_SESSION['account']['id'] = $account['id'];
    $_SESSION['account']['premium'] = $account['premium'];
  }

  public static function logout()
  {
    self::fini();
    redirect_and_exit("/index-new.php");
  }

  public static function requireLogin()
  {
    if (!isset($_SESSION['account']))
      redirect_and_exit("/index-new.php");
  }

  public static function requireLogout()
  {
    if (isset($_SESSION['account']))
      {
        self::fini();
        redirect_and_exit($_SERVER['REQUEST_URI']);
      }
  }

}

SESSION::init();

?>
