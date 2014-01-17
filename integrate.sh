#!/bin/bash

 ##############################################################################
 #                                    yapf                                    #
 #                                                                            #
 #    Copyright (C) 2013  Karl Kronberger, Andreas Grapentin                  #
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

set -e
set -u

## this file is intended to push latest versions of yapf into the projects it has been integrated in.
## subject to change and probably only really useful on my own machine.

files="yapf manage.py"

code=/home/andi/code

# kalindor-legacy
rsync -av --delete --exclude=".*" $files $code/kalindor-legacy/

echo "all up to date"
