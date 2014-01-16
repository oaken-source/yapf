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

class MAIL
{

  /*self::$headers = "MIME-Version: 1.0\r\n"
                  ."Content-type: text/html; charset=utf-8\r\n"
                  ."From: Knights of Kalindor <noreply@kalindor.at>\r\n"
                  ."Reply-To: Recipient Name <receiver@domain3.com>";

  public static function send($to, $subject, $template, $extra_headers = array())
  {
    
    $headers   = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/plain; charset=iso-8859-1";
    $headers[] = "From: Sender Name <sender@domain.com>";
    $headers[] = "Bcc: JJ Chong <bcc@domain2.com>";
    $headers[] = "Reply-To: Recipient Name <receiver@domain3.com>";
    $headers[] = "Subject: {$subject}";
    $headers[] = "X-Mailer: PHP/".phpversion();

    mail($to, $subject, $email, implode("\r\n", $headers));
  }*/

}

?>
