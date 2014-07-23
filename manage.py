#!/usr/bin/python

 ##############################################################################
 #                                    yapf                                    #
 #                                                                            #
 #    Copyright (C) 2013 - 2014  Karl Kronberger, Andreas Grapentin           #
 #                                                                            #
 #    This program is free software: you can redistribute it and/or modify    #
 #    it under the terms of the GNU General Public License as published by    #
 #    the Free Software Foundation, either version 3 of the License, or       #
 #    (at your option) any later version.                                     #
 #                                                                            #
 #    This program is distributed in the hope that it will be useful,         #
 #    but WITHOUT ANY WARRANTY; without even the implied warranty of          #
 #    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           #
 #    GNU General Public License for more details.                            #
 #                                                                            #
 #    You should have received a copy of the GNU General Public License       #
 #    along with this program.  If not, see <http://www.gnu.org/licenses/>.   #
 ##############################################################################

import sys;
import os;

index_php_template = """<?php require_once($_SERVER['DOCUMENT_ROOT']."/yapf/valid_request.php");

RENDERER::setTitle("%s");
RENDERER::setTemplate("%s.php");

?>"""

template_php_template = """<?php require_once($_SERVER['DOCUMENT_ROOT']."/yapf/valid_request.php"); ?>

<h1>%s</h1>
"""

# print usage string to stdout and exit
def usage(msg = "{newpage}"):
  print "usage: `" + sys.argv[0] + " " + msg + "`"
  sys.exit()

# handle errors to stderr
def error(msg):
  sys.exit("%s: %s" % ( sys.argv[0], msg))

def newpage(page):
  if os.path.exists("pages/" + page + "/"):
    error("page '" + page + "' already exists")
  os.makedirs("pages/" + page + "/templates/")
  with open("pages/" + page + "/index.php", "w") as f:
    f.write(index_php_template % (page, page))
  with open("pages/" + page + "/templates/" + page + ".php", "w") as f:
    f.write(template_php_template % page)

# manage args
if len(sys.argv) < 2:
  usage()

if sys.argv[1] == 'newpage':
  if len(sys.argv) != 3:
    usage("newpage <page>")
  newpage(sys.argv[2])
else:
  usage()
