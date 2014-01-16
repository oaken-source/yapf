#!/usr/bin/python

import sys;
import os;

index_php_template = """
<?php require_once($_SERVER['DOCUMENT_ROOT']."/control/valid_request.php"); 

RENDERER::setTitle("%s");
RENDERER::setTemplate("%s");

?>
"""

template_php_template = """
<?php require_once($_SERVER['DOCUMENT_ROOT']."/control/valid_request.php"); ?>

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
