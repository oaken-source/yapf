#!/bin/bash

set -e
set -u

## this file is intended to push latest versions of yapf into the projects it has been integrated in.
## subject to change and probably only really useful on my own machine.

files="control manage.py"

code=/home/andi/code

# kalindor-legacy
cp -Ruv $files $code/kalindor-legacy

echo "all up to date"
